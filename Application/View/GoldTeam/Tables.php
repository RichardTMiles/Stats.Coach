<div class="box box-solid box-warning" style="margin-top: 20px">
    <div class="box-header">
        <h3 class="box-title">Table Selection</h3>
    </div>
    <div class="box-body">
        <?php for ($i = 1; $i < 41; $i++): ?>
            <div class="col-xs-3" style="margin-top: 10px">
                <a href="<?=SITE.'Table'.DS.$i?>" type="button" class="btn btn-default btn-block"><?= $i ?></a>
            </div>
        <?php endfor; ?>
    </div>
    <div class="box-footer">

    </div>
</div>