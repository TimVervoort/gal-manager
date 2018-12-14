<h1>Login <a href="<?php echo $SETTINGS->siteUrl; ?>"><?php echo $SETTINGS->siteName; ?></a></h1>
<p>Login to manage galleries.</p>
<form>
    <input type="text" name="username" placeholder="Username" minlength="2" required />
    <input type="password" name="password" placeholder="Password" minlength="6" required />
    <input type="submit" value="Login" />
</form>