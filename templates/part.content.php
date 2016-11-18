<?php
/**
 * @var OCP\Template $this
 * @var array $_
 */

?>

<div class="tbl">
    <!--    left side    -->
    <div class="tbl_cell left_side">
        <h1><?=$_['projectName']?></h1>
        <?php if(!$_['sd']['flag']){
            echo'Error. Please,<br>'.$_['sd']['error'];
        }else{?>
        <form method="post" id="dashboard" enctype="multipart/form-data">
            <div class="tbl">

                <div class="tbl_cell clb_fields">
                    <div class="tbl">
                        <div class="tbl_cell">Client</div>
                        <div class="tbl_cell"><input type="text" placeholder="Client name" class="client_name" name="client_name"
                                                     value="<?= $_['current_val']['client_name'] ?>" disabled>
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell">Venue</div>
                        <div class="tbl_cell"><input type="text" placeholder="Description 1" <?=$_['disabled']?>
                                                     name="description1" value="<?= $_['current_val']['description1'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell"></div>
                        <div class="tbl_cell"><input type="text" placeholder="Description 2" <?=$_['disabled']?>
                                                     name="description2" value="<?= $_['current_val']['description2'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell"></div>
                        <div class="tbl_cell"><input type="text" placeholder="Street + Number" name="street" <?=$_['disabled']?>
                                                     value="<?= $_['current_val']['street'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell"></div>
                        <div class="tbl_cell"><input type="text" placeholder="ZIP" name="zip" <?=$_['disabled']?>
                                                     value="<?= $_['current_val']['zip'] ?>"> <input
                                type="text"
                                placeholder="City" <?=$_['disabled']?>
                                name="city" value="<?= $_['current_val']['city'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell"></div>
                        <div class="tbl_cell">
                          <input id="drop_countries" type="hidden" <?=$_['disabled']?> value="<?= $_['current_val']['country'] ?>">
                            <?php include "country.list.php"?>

                        </div>
                    </div>
                </div>


                <div class="tbl_cell">
                        <div class="clb_logo tbl">
                            <div class="tbl_cell"><img src="<?= $_['current_val']['image'] ?>" alt="Project Logo" id="project_logo"></div>
                        </div>
                    <?php if (!$_['disabled']){?>
                        <div class="tbl">
                            <div class="tbl_cell txt_left">
                                <div class="uploadLogoBtn">
                                <span id="uploadLogoSpan">Upload</span>
                                <input type="file" id="uploadLogoBtn">
                                </div>
                            </div>
                            <div class="tbl_cell txt_right">
                                <a id="ajax_button_show_files">Choose</a>
                            </div>
                        </div>
                    <?php }?>
                        <div class="uploadedfiles"><ul></ul></div>
                    <div class="list_files"><ul></ul></div>
                    <div id="uploadimg" class="txt_center" style="display: none">
                        <img src="/core/img/loading-small.gif">
                    </div>
                </div>
            </div>
            <div>
                <div>Period of Time</div>
                <div class="dp_block"> from <input type="text" class="datetime" name="start_date" disabled
                                                   value="<?= $_['current_val']['start_date'] ?>"> to <input type="text" disabled
                                                                                                             class="datetime"
                                                                                                             name="end_date"
                                                                                                             value="<?= $_['current_val']['end_date'] ?>">
                </div>
            </div>
            <div class="txt_right">
            <textarea name="comment" <?=$_['disabled']?>
                      placeholder="You can add additional project information"><?= $_['current_val']['comment'] ?></textarea>
                <?php if (!$_['disabled']){?> <input type="submit" value="Submit" id="send_dashboard"><?php }?>
            </div>
        </form>
        <?php }?>

    </div>


    <!--    right side    -->
    <div class="tbl_cell">
        <h1>Statistic</h1>
        <div class="clb_statistic">
            <div class="tbl">
                <div class="tbl_cell">
                    <?=$_['statistic']['tasks_finished']?>
                    <canvas id="elips1" width="50" height="50" class="" data-info="<?=$_['statistic']['tasks_progress']?>"></canvas>
                </div>
                <div class="tbl_cell">of</div>
                <div class="tbl_cell">
                    <?=$_['statistic']['all_tasks']?>
                    <canvas id="elips2" width="50" height="50" class=""></canvas>
                </div>
                <div class="tbl_cell">tasks finished ... <a href="/index.php/apps/owncollab_chart/">finish a task</a></div>
            </div>
            <div class="tbl">
                <div class="tbl_cell">
                    <?=$_['statistic']['participating_talks']?>
                    <canvas id="elips3" width="50" height="50" class="" data-info="<?=$_['statistic']['talks_progress']?>"></canvas>
                </div>
                <div class="tbl_cell">of</div>
                <div class="tbl_cell">
                    <?=$_['statistic']['all_talks']?>
                    <canvas id="elips4" width="50" height="50" class=""></canvas>
                </div>
                <div class="tbl_cell">talks you are participating ... <a href="/index.php/apps/owncollab_talks/begin">participate</a></div>
            </div>
            <div class="tbl">
                <div class="tbl_cell">
                    <?=$_['statistic']['users']?>
                    <canvas id="elips5" width="50" height="50" class=""></canvas>
                </div>
                <div class="tbl_cell">users uploaded</div>
                <div class="tbl_cell">
                    <?=$_['statistic']['all_files']?>
                    <canvas id="elips6" width="50" height="50" class=""></canvas>
                </div>
                <div class="tbl_cell">files ... <a href="/index.php/apps/files">upload a file</a></div>
            </div>
            <div class="tbl">
                <div class="tbl_cell">
                    <?=$_['statistic']['users']?>
                    <canvas id="elips7" width="50" height="50" class=""></canvas>
                </div>
                <div class="tbl_cell">users in</div>
                <div class="tbl_cell">
                    <?=$_['statistic']['all_groups']?>
                    <canvas id="elips8" width="50" height="50" class=""></canvas>
                </div>
                <div class="tbl_cell">groups ... <a href="mailto:?subject=<?=$_['sub_url']?>&body=<?=$_['body_url']?>">invite a user</a></div>
            </div>
        </div>
    </div>
</div>


