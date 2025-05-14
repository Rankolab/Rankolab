<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rankolab Admin - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-light">

    <div id="app" class="d-flex">
        <?php echo $__env->make('partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="flex-grow-1">
            <?php echo $__env->make('partials.topnav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <main class="p-4">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

</body>
</html>
<?php /**PATH D:\xampp\htdocs\rankolab\api\rankolab_backend_with_admin\resources\views/layouts/app.blade.php ENDPATH**/ ?>