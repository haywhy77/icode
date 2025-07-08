

<div class="row">

    <div class="col-12 col-lg-9">

        <div class="row">
            <div class="col-12 col-lg-4 margin-bottom-20">

                <div class="widget invert">
                    <div class="widget__icon_layer widget__icon_layer--right">
                        <span class="li-archive2"></span>
                    </div>
                    <div class="widget__container">
                        <div class="widget__line">
                            <div class="widget__icon">
                                <span class="li-archive2"></span>
                            </div>
                            <div class="widget__title">Approved Applications</div>
                            <div class="widget__subtitle">Submitted</div>
                        </div>
                        <div class="widget__box widget__box--left">
                            <div class="widget__informer"><?= ($summary->success) ?> Approved</div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-12 col-lg-4 margin-bottom-20">

                <div class="widget invert">
                    <div class="widget__icon_layer widget__icon_layer--right">
                        <span class="li-archive2"></span>
                    </div>
                    <div class="widget__container">
                        <div class="widget__line">
                            <div class="widget__icon">
                                <span class="li-archive2"></span>
                            </div>
                            <div class="widget__title">Pending Applications</div>
                            <div class="widget__subtitle">Submitted</div>
                        </div>
                        <div class="widget__box widget__box--left">
                            <div class="widget__informer"><?= ($summary->pending) ?> Pending</div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-12 col-lg-4 margin-bottom-20">

                <div class="widget invert">
                    <div class="widget__icon_layer widget__icon_layer--right">
                        <span class="li-archive2"></span>
                    </div>
                    <div class="widget__container">
                        <div class="widget__line">
                            <div class="widget__icon">
                                <span class="li-archive2"></span>
                            </div>
                            <div class="widget__title">Rejected Applications</div>
                            <div class="widget__subtitle">Submitted</div>
                        </div>
                        <div class="widget__box widget__box--left">
                            <div class="widget__informer"><?= ($summary->rejected) ?> Rejected</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="widget invert widget--items-middle">
                <div class="widget__icon_layer widget__icon_layer--right">
                    <span class="li-database-upload"></span>
                </div>
                <div class="widget__container">
                    <div class="widget__line">
                        <div class="widget__icon">
                            <span class="li-database-upload"></span>
                        </div>
                        <div class="widget__title">Job Listing</div>
                        <div class="widget__subtitle">Picks that matches your skills set</div>
                        
                    </div>
                    <div class="widget__box">
                        <button class="btn btn-outline-secondary btn-sm">More information</button>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-indent-rows margin-bottom-0">
                        <thead>
                            <tr>
                                <th width="40">
                                    <label class="form-check-label"></label>
                                </th>
                                <th width="150">Last Updated</th>
                                <th>Job</th>
                                <th width="100">Salary Range</th>
                                <th width="100">Job Location</th>
                                <th>Role</th>
                                <th width="150">Status</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count=0; foreach (($jobs['data']?:[]) as $key=>$job): $count++; ?>
                                <tr>
                                    <td>
                                        <label class="form-check-label"><?= ($count) ?></label>
                                    </td>
                                    <td>
                                        <strong><?= (explode(' ', $job['created_at'])[0]) ?></strong> <span class="text-muted"><?= (explode(' ', $app['created_at'])[1]) ?></span>
                                    </td>
                                    <td>
                                        <strong><?= ($job['job_title']) ?></strong>
                                    </td>
                                    <td><strong><?= ($job['salary_range']) ?></strong></td>
                                    <td><strong><?= ($app['location']) ?></strong></td>
                                    <td>
                                        <?= ($job['field'])."
" ?>
                                    </td>
                                    <td>
                                        <div class="btn btn-outline-<?= ($app['status']=='OPEN'?'success':'danger') ?> btn-block disabled btn-sm"><?= ($job['status']) ?></div>
                                    </td>
                                    <td>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
    <div class="col-12 col-lg-3">

        <div class="card">
            <div class="widget invert widget--items-middle">
                <div class="widget__icon_layer widget__icon_layer--right">
                    <span class="li-server"></span>
                </div>
                <div class="widget__container">
                    <div class="widget__line">
                        <div class="widget__icon">
                            <span class="li-server"></span>
                        </div>
                        <div class="widget__title">Application Tracker</div>
                        <div class="widget__subtitle">Updates on my applications</div>
                    </div>
                </div>
            </div>
            <div class="card-body padding-left-0">

                <div class="timeline timeline--simple">
                    <?php $count=0; foreach (($tracks['data']?:[]) as $key=>$app): $count++; ?>
                        <div class="timeline__item">
                            <div class="dot dot-<?= ($app['level']=='APPROVE'?'success':($app['level']=='REJECTED'?'danger':'warning')) ?>"></div>
                            <div class="content">
                                <div class="title margin-bottom-0"><?= ($app['level']) ?> <strong><?= ($app['job_title']) ?></strong> <?= (explode(' ', $app['created_at'])[0]) ?> <strong><?= (explode(' ', $app['created_at'])[1]) ?></strong> </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- //END PAGE CONTENT -->

