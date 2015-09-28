<?php /** @var $model Medieval\Application\ViewModels\LoginViewModel */ ?>
<?= isset( $model ) ? $model->error : ''; ?>

<form action="" method="post">
    <input type="text" name="username" placeholder="Username"/>
    <input type="password" name="password" placeholder="Password"/>
    <input type="submit" name="submit" value="Login"/>
</form>