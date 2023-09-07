<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/page_vote.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <title>Page de vote</title>
</head>

<body class="body_vote">

    <h1 class="titre_vote">Géoselfing</h1>

    <?php
    function generateGroupHTML($groupNum, $className)
    {
        $folderPath = '../../../api/groupes_img/Groupe' . $groupNum . '/';
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $files = scandir($folderPath);
        ?>

        <div class="rect_blanc">
            <h2 class="titre_groupe">Groupe n°
                <?php echo $groupNum ?> :
            </h2>
            <button class="<?php echo $className ?>" onclick="showModal<?php echo $groupNum ?>()"
                type="submit">Photos</button>

            <div id="notation<?php echo $groupNum ?>" class="rate<?php echo $groupNum ?>">
                <input type="radio" id="star5_<?php echo $groupNum ?>" name="rate<?php echo $groupNum ?>" value="5" />
                <label for="star5_<?php echo $groupNum ?>" title="text">5 stars</label>
                <input type="radio" id="star4_<?php echo $groupNum ?>" name="rate<?php echo $groupNum ?>" value="4" />
                <label for="star4_<?php echo $groupNum ?>" title="text">4 stars</label>
                <input type="radio" id="star3_<?php echo $groupNum ?>" name="rate<?php echo $groupNum ?>" value="3" />
                <label for="star3_<?php echo $groupNum ?>" title="text">3 stars</label>
                <input type="radio" id="star2_<?php echo $groupNum ?>" name="rate<?php echo $groupNum ?>" value="2" />
                <label for="star2_<?php echo $groupNum ?>" title="text">2 stars</label>
                <input type="radio" id="star1_<?php echo $groupNum ?>" name="rate<?php echo $groupNum ?>" value="1" />
                <label for="star1_<?php echo $groupNum ?>" title="text">1 star</label>
            </div>

            <div id="modal_g<?php echo $groupNum ?>" class="modal_g<?php echo $groupNum ?>">
                <div class="modal-content_g<?php echo $groupNum ?>">
                    <span class="close_g<?php echo $groupNum ?>" onclick="hideModal<?php echo $groupNum ?>()">&times;</span>
                    <form id="addForm">
                        <?php
                        foreach ($files as $file) {
                            $extension = pathinfo($file, PATHINFO_EXTENSION);
                            if (in_array(strtolower($extension), $imageExtensions)) {
                                echo '<img src="' . $folderPath . $file . '" alt="' . $file . '" width="300" height="400">';
                            }
                        }
                        ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="vert"></div>
        <?php
    }
    ?>

    <?php

    $dir = '../../../api/groupes_img/';
    $files = scandir($dir);
    $num_files = count($files) -2;

    for($i = 1; $i <= $num_files; $i++){
        generateGroupHTML($i, "bouton$i");
    }
    
    ?>

    <div class="vert"></div>

    <div class="rect_blanc_envoyer">
        <div class="div_envoyer">
            <button class="envoyer" onclick="submitRating()" type="submit">Envoyer</button>
        </div>
    </div>

    <div class="vert"></div>

    <!-- BAS DE PAGE GENERIQUE -->
    <footer>
        <div class="footer-container">
            <p>Copyright &copy; 2022 GEOSELFING</p>
            <ul>
                <li><a href="politiques_&_conditions.html#politiques">Politique de confidentialité</a></li>
                <li><a href="politiques_&_conditions.html#conditions">Conditions d'utilisation</a></li>
            </ul>
        </div>
    </footer>

    <script src="../js/script_page_vote.js"></script>
</body>

</html>