<?php /** @var Medieval\Areas\TestArea\ViewModels\RegisterViewModel $model */ ?>
<?= isset( $model ) ? $model->getError() : ''; ?>

<div>
    <h2>Register or <span><a href="/user/login">login</a></span></h2>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username"/>
        <input type="password" name="password" placeholder="Password"/>
        <input type="password" name="confirm" placeholder="Confirm Password"/>
        <input type="text" name="name" placeholder="Name"/>
        <input type="submit" name="submit" value="Register"/>
    </form>
</div>