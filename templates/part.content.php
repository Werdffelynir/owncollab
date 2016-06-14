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
        <form method="post" id="dashboard" enctype="multipart/form-data">
            <div class="tbl">

                <div class="tbl_cell clb_fields">
                    <div class="tbl">
                        <div class="tbl_cell">Client</div>
                        <div class="tbl_cell"><input type="text" placeholder="Client name" name="client_name"
                                                     value="<?= $_['current_val']['client_name'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell">Venue</div>
                        <div class="tbl_cell"><input type="text" placeholder="Description 1"
                                                     name="desc1" value="<?= $_['current_val']['desc1'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell">&nbsp;</div>
                        <div class="tbl_cell"><input type="text" placeholder="Description 2"
                                                     name="desc2" value="<?= $_['current_val']['desc2'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell">&nbsp;</div>
                        <div class="tbl_cell"><input type="text" placeholder="Street + Number" name="street"
                                                     value="<?= $_['current_val']['street'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell">&nbsp;</div>
                        <div class="tbl_cell"><input type="text" placeholder="ZIP" name="zip"
                                                     value="<?= $_['current_val']['zip'] ?>"> <input
                                type="text"
                                placeholder="City"
                                name="city" value="<?= $_['current_val']['city'] ?>">
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tbl_cell">&nbsp;</div>
                        <div class="tbl_cell"><input type="text" placeholder="Country as Dropdown=list" name="country"
                                                     value="<?= $_['current_val']['country'] ?>"></div>
                    </div>
                </div>


                <div class="tbl_cell">
                        <div class="clb_logo tbl">
                            <div class="tbl_cell"><img src="<?= $_['current_val']['logo'] ?>" alt="Project Logo" id="project_logo"></div>
                        </div>
                        <div class="tbl">
                            <div class="tbl_cell txt_left">
                                <div class="uploadLogoBtn">
                                <span id="uploadLogoSpan">Upload</span>
                                <input type="file" name="uploadlogo" id="uploadLogoBtn">
                                </div>
                            </div>
                            <div class="tbl_cell txt_right">
                                <a id="ajax_button_show_files">Choose</a>
                            </div>
                        </div>
                        <div class="uploadedfiles"><ul></ul></div>
                    <div class="list_files"><ul></ul></div>
                    <div id="uploadimg" class="txt_center" style="display: none">
                        <img src="/core/img/loading-small.gif">
                    </div>
                </div>
            </div>
            <div>
                <div>Period of Time</div>
                <div class="dp_block"> from <input type="text" class="datetime" name="start_date"
                                                   value="<?= $_['current_val']['start_date'] ?>"> to <input type="text"
                                                                                                             class="datetime"
                                                                                                             name="end_date"
                                                                                                             value="<?= $_['current_val']['end_date'] ?>">
                </div>
            </div>
            <div class="txt_right">
            <textarea name="comment"
                      placeholder="input text field like the Talks input field width some text format features"><?= $_['current_val']['comment'] ?></textarea>
                <input type="submit" value="Submit" id="send_dashboard">
            </div>
        </form>
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


