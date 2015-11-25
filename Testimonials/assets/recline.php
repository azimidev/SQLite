<div class="recline"> 
<form method="post" action="<?php echo($G['SELF']) ?>">
<table><tr>
    <td class="rlbuttons"><div class="nowrap rlbuttons">
        <input type="submit" name="edit" value="Edit" class="edit" />
        <input type="submit" name="delete" value="Del" class="delete" />
    </div></td>
    <td class="rlbyline"><div class="nowrap rlbyline">
    <?php echo($G['byline']) ?>
    </div></td>
    <td class="rltestimonial"><div class="nowrap rltestimonial">
    <?php echo($G['testimonial']) ?>
    </div></td>
</tr></table>
<input type="hidden" name="a" value="edit_del" />
<input type="hidden" name="id" value="<?php echo($G['id']) ?>" />
</form>
</div>
