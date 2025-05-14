

<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Admin Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Overview of Platform Statistics</li>
    </ol>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h2><?php echo e($stats['totalUsers']); ?></h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="text-white stretched-link" href="<?php echo e(route('admin.users.index')); ?>">View all Users</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h5>Websites</h5>
                    <h2><?php echo e($stats['totalWebsites']); ?></h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="text-white stretched-link" href="<?php echo e(route('admin.websites.index')); ?>">View all Websites</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h5>Published Articles</h5>
                    <h2><?php echo e($stats['publishedArticles']); ?></h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="text-white stretched-link" href="<?php echo e(route('admin.articles.index')); ?>">View all Articles</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <h5>Active Licenses</h5>
                    <h2><?php echo e($stats['activeLicenses']); ?></h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="text-white stretched-link" href="<?php echo e(route('admin.licenses.index')); ?>">View all Licenses</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Optionally, add recent users or articles lists below -->

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\rankolab\api\rankolab_backend_with_admin\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>