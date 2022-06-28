<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<table class="table table-licence-items" data-order-col="1" data-order-type="desc">
    <thead>
        <tr>
            <th><?php echo 'No'; ?> #</th>
            <th><?php echo 'Jenis pesawat'; ?></th>
            <th><?php echo 'Tags'; ?></th>
            <th><?php echo 'Item/Lot'; ?></th>

        </tr>
    </thead>
    <tbody>
        <?php $i=1;?>
        <?php foreach($licence_proposed_items as $item){ ?>
            <tr>
                <td ><?php echo $i; ?></td>
                <td><?php echo $item['task_name']; ?></td>
                <td><?php echo $item['tags_name']; ?></td>
                <td><?php echo $item['count']; ?></td>
            </tr>
            <?php $i++; ?>
        <?php } ?>
    </tbody>
</table>
