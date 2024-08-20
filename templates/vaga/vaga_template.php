<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
    global $actual_link;
    $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $siteUrl = get_site_Url();
    $companySlug = get_query_var( 'empresa_slug' ); 
    $vacancyId = get_query_var( 'vaga_id' ); 
    $socialMediaLinks = array("Facebook"=>"https://www.facebook.com/sharer.php?u=$actual_link", "Linkedin"=>"https://www.linkedin.com/sharing/share-offsite/?url=$actual_link", "Twitter"=>"https://twitter.com/share?url=$actual_link&text=", "Whatsapp"=>"https://wa.me/?text=$actual_link - ", "Telegram"=>"https://telegram.me/share/url?url=$actual_link&text=");
    $vacancyPage = $siteUrl . "/empresa/" . $companySlug;
    
    $baseApiUrl = "172.16.1.2:8080/api/publico";
    //$baseApiUrl = "http://www.spring.bantal.com.br/api/publico";

    $curlCompany = curl_init();
    curl_setopt_array($curlCompany, array(
    CURLOPT_URL => "$baseApiUrl/empresas/sigla/$companySlug",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curlCompany);
    curl_close($curlCompany);
    $companyData = json_decode($response);
    $companyId = $companyData[0] -> userId;
    
    global $companyName, $vacancyTitle, $companyLogo, $companyDescription, $companyLogoUrl;
    $companyName = $companyData[0] -> nomeEmpresa;
    $companyLogo = "data:image/jpg;base64," . $companyData[0] -> foto;
    
    file_put_contents("wp-content/uploads/brands/$companySlug.jpg", base64_decode($companyData[0] -> foto));
    
    $companyLogoUrl = "$siteUrl/wp-content/uploads/brands/$companySlug.jpg";

    $companyDescription = $companyData[0] -> objetivo;

?>

<?php
    $curlVacancies = curl_init();
    curl_setopt_array($curlVacancies, array(
    CURLOPT_URL => "$baseApiUrl/vagas-disponives/empresa/$companyId",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $responseVacancies = curl_exec($curlVacancies);

    curl_close($curlVacancies);
    $vacanciesList = json_decode($responseVacancies);

?>

<?php
    $vacancyId= get_query_var( 'vaga_id' ); 
    $curlVacancy = curl_init();
    curl_setopt_array($curlVacancy, array(
    CURLOPT_URL => "$baseApiUrl/vagas-disponives/detalhamento/$vacancyId",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $responseVacancy = curl_exec($curlVacancy);

    $retcode = curl_getinfo($curlVacancy, CURLINFO_HTTP_CODE);

    //RETORNA STATUS 404 QUANDO A EMPRESA NÃO FOR ENCONTRADA
    if($retcode != 200){
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        get_template_part( 404 ); exit();
    }

    // if (curl_errno($curlVacancy)) {
    //    echo $error_msg = curl_error($curlVacancy);
    // }

    curl_close($curlVacancy);
    $vacancyDetails = json_decode($responseVacancy);
    $vacancyTitle = $vacancyDetails[0] -> tituloVaga;
    $vacancyLocation = $vacancyDetails[0] -> localizacaoVaga;
    $shareText = "Vaga para $vacancyTitle - $vacancyLocation";

?>

<?php
    function changeVacancyFieldNameBasedOnVacancyID($fieldName){
        $vacancyId= get_query_var( 'vaga_id' );

        if($vacancyId === "40"){
            $fieldName = "Atendimento ao Cliente";
        }

        return $fieldName;
    }

    function changeVacancyQuantBasedOnVacancyID($quantity){
        $vacancyId= get_query_var( 'vaga_id' );
        
        if($vacancyId === "40"){
            $quantity = "6";
        }

        return $quantity;
    }
?>


<?php
    function pageTitle() {   
        global  $companyName, $vacancyTitle; 
        return "$companyName - Vaga para $vacancyTitle";
    }
    add_filter( 'pre_get_document_title', 'pageTitle', 999 );

    function addMetaTagsToHead(){
        global $actual_link, $companyName, $vacancyTitle, $companyLogoUrl, $companyDescription;

        echo "
            <meta property='og:url' content='$actual_link' />
            <meta property='og:type' content='website' />
            <meta id='og_title' property='og:title' content='$companyName - Vaga para $vacancyTitle.' />
            <meta property='og:description' content='$companyDescription' />
            <meta property='og:image' content='$companyLogoUrl' />
            <meta property='og:image:width'content='1200'/>
            <meta property='og:image:height'content='630'/>
        ";
    };
    add_action('wp_head', 'addMetaTagsToHead', 9999);

    get_header();

?>

<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/templates/styles.css" type="text/css" />
<script src="https://kit.fontawesome.com/85921d822d.js" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<section class="vacancy__section">
    <div class="container">
        <div class="row">
            <div class="main__content">
                <div class="vacancy__header">
                    <h4 class="vacancy__title"><?php echo $vacancyDetails[0] -> tituloVaga; ?></h4>
                    <div class="vacancy__metaData">
                        <div class="vacancy__metaData_item"><i class="fa-solid fa-suitcase"></i> <span class="vacancy__area"> <?php echo changeVacancyFieldNameBasedOnVacancyID($vacancyDetails[0] -> areaAtuacao); ?></span></div>
                        <div class="vacancy__metaData_item"><i class="fa-solid fa-calendar"></i> <span class="vacancy__date"> <?php echo $vacancyDetails[0] -> dataVaga; ?> </span></div>
                        <div class="vacancy__metaData_item"><i class="fa-solid fa-location-dot"></i> <span class="vacancy__local"> <?php echo $vacancyDetails[0] -> localizacaoVaga; ?></span></div>
                    </div>
                </div>

                <div class="vacancy__details">
                    <h2 class="section__title">Descrição da Vaga</h2>

                    <div class="vacancy__description">
                        <?php echo $vacancyDetails[0] -> descricaoVaga; ?>
                    </div>

                </div>

                  <div class="socialMenu__wrapper">
                        <span>Compartilhe essa vaga</span>
                        <div class="socialMenu__buttons_wrapper">
                            <a target="_blank" href="<?php echo $socialMediaLinks["Facebook"]; ?>" class="facebook__link"><i class="fa-brands fa-facebook"></i></a>
                            <a target="_blank" href="<?php echo $socialMediaLinks["Linkedin"]; ?>" class="linkedin__link"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a target="_blank" href="<?php echo $socialMediaLinks["Twitter"] . $companyName . ' - ' . $shareText; ?>" class="twitter__link"><i class="fa-brands fa-twitter"></i></a>
                            <a target="_blank" href="mailto:?subject=<?php echo $companyName . ' - ' . $shareText; ?>&body=Link - <?php echo $actual_link; ?>" class="email__link"><i class="fa-solid fa-envelope"></i></a>
                            <a target="_blank" href="<?php echo $socialMediaLinks["Whatsapp"] . $companyName . ' - ' . $shareText; ?>" class="whatsapp__link"><i class="fa-brands fa-whatsapp"></i></a>
                            <a target="_blank" href="<?php echo $socialMediaLinks["Telegram"]. $companyName . ' - ' . $shareText;; ?>" class="telegram__link"><i class="fa-brands fa-telegram"></i></a>
                            <a href="#" class="clipBoard__button">
                                <i class="fa-solid fa-copy"></i>
                                Copiar Link
                            </a>
                        </div>
                    </div>

                    <div class="btn__primary btn_toggler">
                        <span class="btn__link" href="">Quero me candidatar</span>
                    </div>

                    <div class="btn__group_wrapper">
                        <div class="btn__primary">
                            <a class="btn__link company__profile_btn" href="https://recrutamento.bantal.com.br/cadastro" target="_blank">Crie sua Conta</a>
                        </div>
                        <div class="btn__primary">
                            <a class="btn__link company__profile_btn" href="https://recrutamento.bantal.com.br/auth/login" target="_blank">Fazer Login</a>
                        </div>
                    </div>
            </div>

            <aside class="sidebar">
                <div class="company__sumary">
                    <div class="company__header_logo">
                        <img src="<?php echo $companyLogo; ?>" alt="logo-da-empresa" />
                    </div>

                    <div class="company__sumary_card">
                        <div class="company_card">
                            <h4 class="company_card_title"><?php echo $companyName; ?></h4>

                            <div class="company_card_subtitle">
                                <span>Vagas: <strong class="company__vacancies"><?php echo changeVacancyQuantBasedOnVacancyID($companyData[0] -> quantidade); ?></strong></span>
                            </div>

                            <div class="btn__primary">
                                <a class="btn__link company__profile_btn" href="<?php echo $siteUrl . '/empresa/' . $companySlug; ?>">Ver Perfil</a>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>


<section class="vacancy__carousel_section">
    <div class="container">
        <div class="company__vancacies_header">
            <h3 class="section__title">Outras Vagas Disponíveis</h3>
        </div>

        <div class="vacancy__carousel_wrapper">
            <div class="vacancy__carousel">
                <?php foreach($vacanciesList as $vacancy){ ?>
                    <div class="vacancy__carousel_card">
                        <h4 class="vacancy__title"><?php echo $vacancy -> tituloVaga; ?></h4>
                        <div class="vacancy__metaData">
                                <span><i class="fa-solid fa-calendar"></i> <?php echo $vacancy -> dataVaga; ?> </span>
                            <span><i class="fa-solid fa-location-dot"></i> <?php echo $vacancy -> localizacaoVaga; ?> </span>
                        </div>

                        <div class="btn__primary">
                            <a class="btn__link" href="<?php echo $vacancyPage . '?vaga_id=' . $vacancy->idVaga; ?>">Ver vaga</a>
                        </div>
                    </div>
               <?php } ?>
            </div>
        </div>  
    </div>
</section>

<input id="hidden_url" type="text" value="<?php echo $actual_link; ?>" style="display: none;" />


<script>
    const clipBoardBtn = document.querySelectorAll(".clipBoard__button")
    clipBoardBtn.forEach((btn) => {
        btn.addEventListener("click", function(){
            const hiddenUrl = document.querySelector("#hidden_url");
            hiddenUrl.select();
            navigator.clipboard.writeText(hiddenUrl.value);
            alert("Link copiado");
        })
    })
    
    

    
    $(document).ready(function () {
        $(".vacancy__carousel").slick({
            slidesToShow: 2,
            slidesToScroll: 1,
            dots: false,
            speed: 500,
            autoplay: true,
            nextArrow: '<button type="button" class="slick-next">Next</button>',
            prevArrow: '<button type="button" class="slick-prev">Previous</button>',
            responsive: [
            {
                breakpoint: 768,
                settings: {
                slidesToShow: 1,
                dots: true,
                },
            },
            {
                breakpoint: 300,
                settings: "unslick", // destroys slick
            },
            ],
        });
    });


    const btnGroup = document.querySelector(".btn__group_wrapper");
    const btnToggler = document.querySelector(".btn_toggler");

    btnToggler.addEventListener("click", function(){
        btnGroup.classList.toggle("show-btn-group");
    })
   
</script>


<?php get_footer() ?>