<?php

declare(strict_types=1);

$flash = session()->getFlash();
?>

<h1>Test Page</h1>
<p>This is a test page.</p>

<form method="post" action="/test">
    <label for="input">Enter something:</label>
    <input type="text" id="input" name="input" required>
    <button type="submit">Submit</button>
</form>

<?php if ($flash): ?>
    <br>
    <div class="warning">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>
