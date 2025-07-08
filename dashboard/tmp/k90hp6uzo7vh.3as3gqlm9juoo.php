
<form method="get">
    <input type="hidden" name="session_csrf" value="<?= ($CSRF) ?>" />
    <div class="row">
        <div class="col-8">
            <div class="row">
                <?php if (isset($byName) && $byName==true): ?>
                    
                        <div class="col-4  d-none d-md-block">
                            <input type="text" name="name" class="form-control" placeholder="by name" />
                        </div>
                    
                <?php endif; ?>
                <div class="col-4 ">
                    
                    <select class="form-control actionWithSelected" name="status" tabindex="-1" aria-hidden="true">
                        <option value="">by status</option>
                        <?php $count=0; foreach ((explode(' ', $status)?:[]) as $key=>$app): $count++; ?>
                            <option value="<?= ($app) ?>"><?= ($app) ?></option>
                        <?php endforeach; ?>
                    </select>
        
                </div>
                <div class="col-2 d-none d-md-block">
        
                    <select class="form-control customPeriod" name="duration" tabindex="-1" aria-hidden="true">
                        <option value="">by date</option>
                        <option value="1">This month</option>
                        <option value="2">Prev month</option>
                        <option value="3">Other</option>
                    </select>
        
                </div>
                <div class="col-2 d-none d-md-block">
                    <button type="submit" class="btn btn-secondary btn-block btn-click">Filter Records</button>
                </div>
            </div>
            
        </div>
        
        <div class="col-4 d-none d-md-block">
            <div class="row">
                <div class="col-3 py-2"><label>Export: </label></div>
                <div class="col-9 form-group">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-light btn-click" data-href="<?= ($BASE) ?>/download/csv" data-target="_blank">CSV</button>
                        <button type="button" class="btn btn-light btn-click" data-href="<?= ($BASE) ?>/download/xls" data-target="_blank">EXCEL</button>
                        <button type="button" class="btn btn-light btn-click" data-href="<?= ($BASE) ?>/download/pdf" data-target="_blank">PDF</button>
                        <button type="button" class="btn btn-light btn-click" data-href="<?= ($BASE) ?>/download/print" data-target="_blank">PRINT</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>