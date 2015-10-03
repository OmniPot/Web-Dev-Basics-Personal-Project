<?php /** @var Medieval\Areas\TestArea\ViewModels\LoginViewModel $model */ ?>
<?= isset( $model ) ? $model->getError() : ''; ?>

<div>
    <h2>Login or <span><a href="/user/register">register</a></span></h2>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username"/>
        <input type="password" name="password" placeholder="Password"/>
        <input type="submit" name="submit" value="Login"/>
    </form>
</div>