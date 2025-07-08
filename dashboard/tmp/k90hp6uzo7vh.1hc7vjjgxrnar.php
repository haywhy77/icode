<div class="row">
    <div class="col-12 col-lg-12">

        
        <div class="card">
            <div class="widget invert widget--items-middle">
                <div class="widget__icon_layer widget__icon_layer--right">
                    <span class="li-folder-user"></span>
                </div>
                <div class="widget__container">
                    <div class="widget__line">
                        <div class="widget__icon">
                            <span class="li-folder-user"></span>
                        </div>
                        <div class="widget__title">Registered Applicants</div>
                        <div class="widget__subtitle">Summary information about employer</div>
                    </div>
                    <!-- <div class="widget__box">
                        <button class="btn btn-secondary btn-lg btn-click" data-href="<?= ($BASE) ?>/candidates/new">Add Employer</button>
                    </div> -->
                </div>
            </div>
            <div class="card-body">
                
                <?php if ($SESSION['account']=='ADMIN'): ?>
                    
                        <?php echo $this->render('includes/filter.html',NULL,['byName'=>true,'status'=>'ACTIVE DEACTIVATED CANCELLED']+get_defined_vars(),0); ?>
                    
                    <?php else: ?>
                        <?php echo $this->render('includes/hunt.html',NULL,['locations'=>'$locations']+get_defined_vars(),0); ?>
                    
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-indent-rows margin-bottom-0">
                        <thead>
                            <tr align="center">
                                <th width="100">
                                    <div class="form-check">
                                        <label class="form-check-label">#</label>
                                    </div>
                                </th>
                                <th>Name</th>
                                <th >Email</th>
                                <th width="200">Contact number</th>
                                <th width="200">Gender</th>
                                <th width="200">VERIFIED?</th>
                                <th width="150">Status</th>
                                <th>Trash Account</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count=0; foreach (($candidates['data']?:[]) as $key=>$company): $count++; ?>
                                <tr align="center">
                                    <td>
                                        <div class="form-check">
                                            <label class="form-check-label"><?= ($count) ?></label>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><a href="<?= ($BASE) ?>/candidates/<?= ($company['id']) ?>"><?= ($company['name']) ?></a></strong>
                                    </td>
                                    <?php if ($SESSION['account']=='ADMIN'): ?>
                                        
                                            <td><?= ($company['email']) ?></td>
                                            <td><?= ($company['phone']) ?></td>
                                        
                                        <?php else: ?>
                                            <td><?= ($maskString($company['email'])) ?></td>
                                            <td><?= ($maskString($company['phone'])) ?></td>
                                        
                                    <?php endif; ?>
                                    
                                    <td><?= ($company['gender']) ?></td>
                                    <td>
                                        <div class="btn <?= ($company['isVerify']=='VERIFIED'?'btn-outline-success':'btn-outline-danger') ?> btn-block disabled btn-sm"><?= ($company['isVerify']) ?></div>
                                    </td>
                                    <td>
                                        <div class="btn <?= ($company['status']=='ACTIVE'?'btn-outline-success':'btn-outline-danger') ?> btn-block disabled btn-sm"><?= ($company['status']) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($SESSION['account']=='ADMIN'): ?>
                                            
                                                
                                                <button class="btn btn-secondary btn-icon btn-sm btn-delete" data-value="<?= ($company['id']) ?>"><span class="fa fa-trash"></span></button>
                                            
                                        <?php endif; ?>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                        </tbody>
                    </table>
                </div>
                <?php echo $this->render('includes/pagination.html',NULL,['data'=>$candidates,'url'=>'/candidates']+get_defined_vars(),0); ?>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        
        $('.btn-delete').on('click', function(){
            if(confirm("Deleting this applicant will also remove all places where the applicant is attached to it. Are you sure you still want to continue?")){
                app._loading.show($(".table-responsive"),{spinner: true});
                const values={
                    id: $(this).attr('data-value'),
                    action: 'CANDIDATE'
                }
                $.post("<?= ($BASE) ?>/jobs/misc-trash", values, function(data, error){
                    console.log(data, error);
                    
                    if(data.status){
                        $(".btn-closes").trigger( "click" );
                        window.location.reload();
                    }else{
                        $(".btn-closes").trigger( "click" );
                        $(".alert-body").html(data.message);
                        $(".modal-trigger")[0].click;
                    }
                    setTimeout(function(){
                        app._loading.hide($(".table-responsive"));
                    },2000);
                })
            }
        })
    });
    
</script>