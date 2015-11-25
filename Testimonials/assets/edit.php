<div id="testimonialForm"> 
<div id="topmatter">
<?php echo($G['MESSAGES']); echo($G['ERRORS']); ?>
</div>
<h1> <?php echo($G['pageTitle']) ?> </h1>
<form method="post" action="<?php echo($G['SELF']) ?>" name="testimonial">
    <p>Testimonial: </p>
    <textarea name="testimonial"><?php echo($G['testimonial']) ?></textarea><br/>
    <p>Byline: </p>
    <input type="text" name="byline" value="<?php echo($G['byline']) ?>"><br/>
    <input type="submit" name="update" value="Update Testimonial" />
    <input type="submit" name="cancel" value="Cancel" />
<?php echo($G['HIDDENS']) ?>
</form>
</div>

<!-- set the focus to the testimonial field -->
<script language="javascript">
    document.testimonial.testimonial.focus();
</script>
