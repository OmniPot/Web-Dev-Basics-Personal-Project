<?php /** @var Medieval\Areas\ProfileArea\ViewModels\ProfileViewModel $model */ ?>

<div>
    <h2>Profile of <?= $model->getUsername(); ?></h2>

    <a href="/home/welcome">Home</a>

    <form action="/user/logout" method="POST">
        <input type="submit" name="logout" value="logout">
    </form>
</div>