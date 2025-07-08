
<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card margin-bottom-0">
            <div class="card-body">
                <?php echo $this->render('includes/filter.html',NULL,['status'=>'PENDING INREVIEW INTERVIEWED APPROVED REJECTED EMPLOYED']+get_defined_vars(),0); ?>
                <div class="table-responsive margin-top-20">
                    <table class="table table-striped table-bordered margin-bottom-20" style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th width="30">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="">
                                    <label class="form-check-label"></label>
                                </div>
                                </th>
                                <th>Date</th>
                                <th>Applicant</th>
                                <th>Job Title</th>
                                
                                <th>Location</th>
                                <th>Position</th>
                                <th width="100">Status</th>
                                <th width="100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count=0; foreach (($applications['data']?:[]) as $key=>$app): $count++; ?>
                                <tr>
                                    <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="">
                                        <label class="form-check-label"><?= ($count) ?></label>
                                    </div>
                                    </td>
                                    <td><?= ($app['created_at']) ?></td>
                                    <td><a href="<?= ($BASE) ?>/candidates/<?= ($app['cand_id']) ?>" class="text-danger text-bold"><?= ($app['candidate']) ?><br><small><?= ($app['email']) ?></small></a></td>
                                    <td><a href="<?= ($BASE) ?>/jobs/<?= ($app['job_id']) ?>" class="text-danger text-bold"><?= ($app['job_title']) ?></a></td>

                                    <td><?= ($app['location']) ?></td>
                                    <td><?= ($app['field']) ?></td>
                                    <td>
                                        <button class="btn btn-outline-<?= ($app['status']=='APPROVED'?'success':($app['status']=='REJECTED'?'danger':'warning')) ?> btn-block btn-sm"><?= ($app['status']) ?></button>
                                    </td>
                                    <td>
                                        <button class="btn btn-secondary btn-sm btn-click" data-href="<?= ($BASE) ?>/application/<?= ($app['id']) ?>">View Application</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                        </tbody>
                    </table>
                </div>
                <?php echo $this->render('includes/pagination.html',NULL,['data'=>$applications,'url'=>'/applications']+get_defined_vars(),0); ?>
            </div>
        </div>
    </div>
</div>