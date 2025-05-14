


<?php $__env->startSection('title', 'Manage Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Users Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Users</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i> User List
            </div>
            <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search users...">
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration + ($users->currentPage() - 1) * $users->perPage()); ?></td>
                            <td><?php echo e($user->username ?? '-'); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e(ucfirst($user->role)); ?></td>
                            <td>
                                <span class="badge <?php echo e($user->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
                                    <?php echo e(ucfirst($user->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e($user->created_at->format('M d, Y')); ?></td>
                            <td class="text-center">
                                <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php echo e($users->links('pagination::bootstrap-5')); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\rankolab\api\rankolab_backend_with_admin\resources\views/admin/users/index.blade.php ENDPATH**/ ?>