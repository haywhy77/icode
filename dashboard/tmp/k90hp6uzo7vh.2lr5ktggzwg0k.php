<?php $query=$SERVER['QUERY_STRING']; ?>
<set url=<?= (str_replace($query, "", $SERVER['REQUEST_URI'])) ?> />
<div class="row margin-bottom-20">
    <div class="col-12 col-lg-4">
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php if ($data['currentPage']>1): ?>
                    <li class="page-item"><a class="page-link" href="<?= ($BASE) ?><?= ($url) ?><?= ($query?'?'.$query.'&':'?') ?>page=<?= ($data['currentPage'] - 1) ?>">Previous</a></li>
                    <?php else: ?><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                <?php endif; ?>
                <?php if ($data['totalPages']>1): ?>
                    
                        <?php $start='1'; ?>
                        <?php $limit='5'; ?>
                        <?php $total=ceil($data['totalPages']/$limit); ?>
                        <?php $offset=($data['currentPage']-1) * $limit; ?>
                        <?php $limitAll=$data['currentPage']<=$limit ? $limit: $limit; ?>
                        <!-- <?= ($data['totalPages']) ?>/<?= ($total) ?>/<?= ($limitAll) ?> -->
                        <?php for ($i=1;$i <= $limit;$i++): ?>
                            <?php if ($i==$data['currentPage']): ?>
                                <li class="page-item active"><a class="page-link"><?= ($i) ?></a></li>
                                <?php else: ?><li class="page-item"><a class="page-link" href="<?= ($BASE) ?><?= ($url) ?><?= ($query?'?'.$query.'&':'?') ?>page=<?= ($i) ?>"><?= ($i) ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>
                    
                    <?php else: ?>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    
                <?php endif; ?>
                
                <?php if ($data['currentPage'] < $data['totalPages']): ?>
                    <li class="page-item"><a class="page-link" href="<?= ($BASE) ?><?= ($url) ?><?= ($query?'?'.$query.'&':'?') ?>page=<?= ($data['currentPage'] + 1) ?>">Next</a></li>
                    <?php else: ?><li class="page-item"><a class="page-link" href="#">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>