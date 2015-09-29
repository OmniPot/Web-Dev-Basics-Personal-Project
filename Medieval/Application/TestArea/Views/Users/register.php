<?php /** @var $model Medieval\Application\TestArea\ViewModels\LoginViewModel */ ?>
<?= isset( $model ) ? $model->error : ''; ?>

<h2>Register</h2>
<form action="" method="post">
    <input type="text" name="username" placeholder="Username"/>
    <input type="password" name="password" placeholder="Password"/>
    <input type="submit" name="submit" value="Register"/>
</form>