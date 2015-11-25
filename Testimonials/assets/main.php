<div id="testimonialForm"> 
<div id="topmatter">
<?php echo($G['MESSAGES']); echo($G['ERRORS']); ?>
</div>
<h1> <?php echo($G['pageTitle']) ?> </h1>
<form name="testimonial" method="post" action="<?php echo($G['SELF']) ?>">
    <p>Testimonial: </p>
    <textarea name="testimonial"><?php echo($G['testimonial']) ?></textarea><br/>
    <p>Byline: </p>
    <input type="text" name="byline" value="<?php echo($G['byline']) ?>" /><br/>
    <input class="formSubmit" type="submit" value="Add Testimonial" />
<?php echo($G['HIDDENS']) ?>
</form>
<div id="reclist">
<h1> Testimonials in the database </h1>
<?php echo($G['CONTENT']) ?>
</div>
</div>

<!-- set the focus to the testimonial field -->
<script language="javascript">
    document.testimonial.testimonial.focus();
</script>
