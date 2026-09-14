<?php

session_start();

require_once 'db.php';


/* Admin only */
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit;
}


/* Must come from the add motorcycle form */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit;
}


/* Get form values */
$motorcycle_name =
    trim($_POST['motorcycle_name'] ?? '');

$price_per_day =
    $_POST['price_per_day'] ?? '';

$total_units =
    $_POST['total_units'] ?? '';


/* Validate fields */
if (
    $motorcycle_name === '' ||
    mb_strlen($motorcycle_name) > 100 ||
    !is_numeric($price_per_day) ||
    !ctype_digit((string) $total_units)
) {
    header("Location: admin.php?motorcycle=error");
    exit;
}


$price_per_day =
    (float) $price_per_day;

$total_units =
    (int) $total_units;


if (
    $price_per_day <= 0 ||
    $total_units < 0
) {
    header("Location: admin.php?motorcycle=error");
    exit;
}


/* Validate uploaded image */
if (
    !isset($_FILES['motorcycle_image']) ||
    $_FILES['motorcycle_image']['error'] !== UPLOAD_ERR_OK
) {
    header("Location: admin.php?motorcycle=error");
    exit;
}


$image =
    $_FILES['motorcycle_image'];


/* Maximum 5MB */
if ($image['size'] > 5 * 1024 * 1024) {
    header("Location: admin.php?motorcycle=error");
    exit;
}


$image_info =
    getimagesize($image['tmp_name']);

if ($image_info === false) {
    header("Location: admin.php?motorcycle=error");
    exit;
}


$allowed_types = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG => 'png'
];


if (!isset($allowed_types[$image_info[2]])) {
    header("Location: admin.php?motorcycle=error");
    exit;
}


$extension =
    $allowed_types[$image_info[2]];


/* Prepare upload folder */
$upload_directory =
    __DIR__ . '/uploads/motorcycles';


if (!is_dir($upload_directory)) {
    if (
        !mkdir(
            $upload_directory,
            0755,
            true
        )
    ) {
        header("Location: admin.php?motorcycle=error");
        exit;
    }
}


/* Create unique filename */
$image_filename =
    'motorcycle_' .
    bin2hex(random_bytes(8)) .
    '.' .
    $extension;


$destination =
    $upload_directory .
    '/' .
    $image_filename;


if (
    !move_uploaded_file(
        $image['tmp_name'],
        $destination
    )
) {
    header("Location: admin.php?motorcycle=error");
    exit;
}


$image_path =
    'uploads/motorcycles/' .
    $image_filename;


try {

    /*
        Check if this motorcycle name already exists.

        Because motorcycle_name is UNIQUE, an old motorcycle
        that was removed with is_active = 0 cannot be inserted
        again as a new row.

        If it exists but is inactive, reactivate and update it.
    */
    $check_stmt =
        $conn->prepare("
            SELECT
                id,
                is_active,
                image_path
            FROM motorcycle_inventory
            WHERE motorcycle_name = ?
            LIMIT 1
        ");


    if (!$check_stmt) {
        throw new RuntimeException(
            "Unable to prepare motorcycle check."
        );
    }


    $check_stmt->bind_param(
        "s",
        $motorcycle_name
    );


    if (!$check_stmt->execute()) {
        $check_stmt->close();

        throw new RuntimeException(
            "Unable to check motorcycle."
        );
    }


    $result =
        $check_stmt->get_result();

    $existing =
        $result->fetch_assoc();

    $check_stmt->close();


    /* Motorcycle already exists and is active */
    if (
        $existing &&
        (int) $existing['is_active'] === 1
    ) {
        if (file_exists($destination)) {
            unlink($destination);
        }

        header(
            "Location: admin.php?motorcycle=exists"
        );
        exit;
    }


    /*
        Motorcycle existed before but was removed.
        Reactivate it instead of inserting a duplicate name.
    */
    if ($existing) {

        $motorcycle_id =
            (int) $existing['id'];

        $old_image_path =
            $existing['image_path'] ?? '';


        $stmt =
            $conn->prepare("
                UPDATE motorcycle_inventory
                SET
                    price_per_day = ?,
                    image_path = ?,
                    total_units = ?,
                    available_units = ?,
                    is_active = 1
                WHERE id = ?
            ");


        if (!$stmt) {
            throw new RuntimeException(
                "Unable to prepare motorcycle reactivation."
            );
        }


        $stmt->bind_param(
            "dsiii",
            $price_per_day,
            $image_path,
            $total_units,
            $total_units,
            $motorcycle_id
        );


        if (!$stmt->execute()) {
            $stmt->close();

            throw new RuntimeException(
                "Unable to reactivate motorcycle."
            );
        }


        $stmt->close();


        /*
            Delete the old uploaded image only if it was one
            of the runtime motorcycle uploads.
        */
        if (
            $old_image_path !== '' &&
            str_starts_with(
                $old_image_path,
                'uploads/motorcycles/'
            )
        ) {
            $old_image_file =
                __DIR__ . '/' .
                $old_image_path;

            if (
                file_exists($old_image_file) &&
                $old_image_file !== $destination
            ) {
                unlink($old_image_file);
            }
        }


        header(
            "Location: admin.php?motorcycle=added"
        );
        exit;
    }


    /* Brand-new motorcycle */
    $stmt =
        $conn->prepare("
            INSERT INTO motorcycle_inventory
            (
                motorcycle_name,
                price_per_day,
                image_path,
                total_units,
                available_units,
                is_active
            )
            VALUES (?, ?, ?, ?, ?, 1)
        ");


    if (!$stmt) {
        throw new RuntimeException(
            "Unable to prepare motorcycle insert."
        );
    }


    $stmt->bind_param(
        "sdsii",
        $motorcycle_name,
        $price_per_day,
        $image_path,
        $total_units,
        $total_units
    );


    if (!$stmt->execute()) {
        $stmt->close();

        throw new RuntimeException(
            "Unable to add motorcycle."
        );
    }


    $stmt->close();


    header(
        "Location: admin.php?motorcycle=added"
    );
    exit;


} catch (Throwable $e) {

    error_log(
        "Add motorcycle error: " .
        $e->getMessage()
    );


    if (file_exists($destination)) {
        unlink($destination);
    }


    header(
        "Location: admin.php?motorcycle=error"
    );
    exit;
}

?>
