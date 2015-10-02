<?php /** @var Medieval\Areas\TestArea\ViewModels\LoginViewModel $model */ ?>
<?= isset( $model ) ? $model->error : ''; ?>

<h2>Login</h2>
<form action="" method="post">
    <input type="text" name="username" placeholder="Username"/>
    <input type="password" name="password" placeholder="Password"/>
    <input type="submit" name="submit" value="Login"/>
</form>