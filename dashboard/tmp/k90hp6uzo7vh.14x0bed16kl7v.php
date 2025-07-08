


<!-- PAGE CONTAINER -->
<div class="page__container invert">
    <nav class="horizontal-navigation">
        <button class="btn btn-light btn--icon" data-action="horizontal-show"><span class="fa fa-bars"></span> Toggle navigation</button>
            <ul>
                <?php foreach (($MENUS?:[]) as $menu): ?>
                    <li class="openable">
                        <?php if ($menu->type=='simple'): ?>
                            
                                <a href="<?= ($BASE) ?><?= ($menu->url) ?>">
                                    <span class="icon li-<?= ($menu->icon) ?>"></span>
                                    <span class="text"><?= ($menu->label) ?></span>
                                </a>
                            
                            <?php else: ?>
                                <a href="#">
                                    <span class="icon li-<?= ($menu->icon) ?>"></span>
                                    <span class="text"><?= ($menu->label) ?></span>
                                </a>
                            
                        <?php endif; ?>
                        <?php if (isset($menu->child)): ?>
                            
                                <ul>
                                    <?php foreach (($menu->child?:[]) as $child): ?>
                                        <li>
                                            <a href="<?= ($BASE) ?><?= ($child->url) ?>" class="no-icon">
                                                <span class="text"><?= ($child->label) ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
    </nav>
</div>
<!-- //END PAGE CONTAINER -->