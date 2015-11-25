<div id="testimonialForm"> 
<div id="topmatter">
<?php echo($G['MESSAGES']); echo($G['ERRORS']); ?>
</div>
<h1> <?php echo($G['pageTitle']) ?> </h1>
<form method="post" action="<?php echo($G['SELF']) ?>">
    <h2>Testimonial</h2>
    <p class="delconf_testimonial"><?php echo($G['testimonial']) ?></p><br/>
    <h2>Byline</h2>
    <p class="delconf_byline"><?php echo($G['byline']) ?></p><br/>
    <input type="submit" name="delete" value="Delete Testimonial" />
    <input type="submit" name="cancel" value="Cancel" />
<?php echo($G['HIDDENS']) ?>
</form>
</div>
