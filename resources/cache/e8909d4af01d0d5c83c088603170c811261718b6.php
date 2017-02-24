<?php $__env->startSection('title', 'Sample'); ?>

<?php $__env->startSection('content'); ?>
	<?php /* This comment will not be present in the rendered HTML 
   <h3>Hello <?php echo e($name); ?></h3>

   <p>Tech:</p>
   <ul>
   <?php foreach( $tech as $tech): ?>
   	<li><?php echo e($tech); ?></li>
   <?php endforeach; ?>
   </ul>

   <p>Today is <?php echo e(date('F d, Y')); ?>.</p>
*/ ?>
   <div class="container">

        <form class="form-signin" action="/authenticate/" method="POST">
          <h2 class="form-signin-heading">Please sign in</h2>
          <label for="inputEmail" class="sr-only">Email address</label>
          <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
          <label for="inputPassword" class="sr-only">Password</label>
          <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
          <div class="checkbox">
            <label>
              <input type="checkbox" value="remember-me"> Remember me
            </label>
          </div>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>

      </div> <!-- /container -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>