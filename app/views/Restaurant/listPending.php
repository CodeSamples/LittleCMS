<?php if(is_array($response) && sizeof($response) > 0): ?>
<table id="restaurantList">
    <thead>
        <th><?php echo ID; ?></th>
        <th><?php echo NAME; ?></th>
        <th><?php echo ACTIONS; ?></th>
    </thead>
    <tbody>
    <?php 

        foreach ($response as $single): 
            $params = array(
                'restaurant_id' => $single->id,
                'redirect_url' => 'Dashboard/admin'
                );

    ?>
    <tr id="restaurant_<?php echo $single->id; ?>">
        <td><?php echo $single->id; ?></td>
        <td><?php echo $single->name; ?></td>
        <td>
            <a class="see" href="/Dashboard/manager/?restaurant_id=<?php echo $single->id; ?>" target="_self">
                <?php echo SEE; ?>
            </a>
            <a class="preview" data-restid="<?php echo $single->id; ?>" target="_blank">
                <?php echo PREVIEW; ?>
            </a>
            <a class="approve" href="/api/Restaurant/approve/?<?php echo http_build_query($params); ?>">
                <?php echo APPROVE; ?>
            </a>
            <a class="reject" href="/api/Restaurant/reject/?<?php echo http_build_query($params); ?>">
                <?php echo REJECT; ?>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div id="previewModal" title="Previsualización de cambios en Sal! Web">
    <iframe id="previewIframe" width="100%" height="95%"></iframe>
</div>
<script type="text/javascript">
    $('.preview').click(function(){
        var rest_id = $(this).data("restid");
        $( "#previewModal" ).dialog({
            autoOpen: true,
            height: $(window).height() - 50,
            width: $(window).width() - 50,
            modal: true
        });
        $("#previewIframe").attr("src","/Restaurant/salPreview/?restaurant_id="+rest_id);
    /*    $.ajax({
			url: '/Restaurant/salPreview/',
			type: 'get',
			data: { 'restaurant_id': rest_id },
			success: function(response) {
				$('#previewModal').html(response);
			}
		})*/
    });
</script>
<?php else: ?>
<p><?php echo APPROVAL_EMPTY; ?></p>
<?php endif; ?>