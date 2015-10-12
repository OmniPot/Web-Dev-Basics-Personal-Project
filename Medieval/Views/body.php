<?php /** @var Medieval\ViewModels\WelcomeViewModel $model */ ?>
<div>
    <a href="/profile/me">Profile</a>

    <form action="/user/logout" method="POST">
        <input type="submit" name="logout" value="logout">
    </form>
</div>