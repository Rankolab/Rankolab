<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h3><?php echo e($stats['totalUsers']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Websites</h5>
                    <h3><?php echo e($stats['totalWebsites']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>Published Articles</h5>
                    <h3><?php echo e($stats['publishedArticles']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Active Licenses</h5>
                    <h3><?php echo e($stats['activeLicenses']); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\rankolab\api\rankolab_backend_with_admin\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>