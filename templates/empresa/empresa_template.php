<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    $siteUrl = get_site_Url();
    $currentPage = get_query_var( 'page' ) ? get_query_var( 'page' ) : 1;
    $vacancyPage = $siteUrl . "/empresa";
    $companySlug = get_query_var( 'empresa_slug' ); 
    
    $baseApiUrl = "172.16.1.2:8080/api/publico";
    //$baseApiUrl = "https://www.spring.bantal.com.br/api/publico";
    
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

    $retcode = curl_getinfo($curlCompany, CURLINFO_HTTP_CODE);

    //RETORNA STATUS 404 QUANDO A EMPRESA NÃO FOR ENCONTRADA
    if($retcode != 200){
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        get_template_part( 404 );
        exit();
    }

    // if (curl_errno($curlCompany)) {
    //    echo $error_msg = curl_error($curlCompany);
    // }

    curl_close($curlCompany);
    
    global $companyName, $vacancyTitle, $companyLogo, $companyDescription, $companyLogoUrl;
    $companyData = json_decode($response);
    $companyId = $companyData[0] -> userId;
    $companyName = $companyData[0] -> nomeEmpresa;
    $companyLogo = $companyData[0] -> foto;
    $companyDescription = $companyData[0] -> objetivo;
    file_put_contents("wp-content/uploads/brands/$companySlug.jpg", base64_decode($companyData[0] -> foto));
    
    $companyLogoUrl = "$siteUrl/wp-content/uploads/brands/$companySlug.jpg";

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

    $responseVacancy = curl_exec($curlVacancies);
    $retcodeVacancy = curl_getinfo($curlVacancies, CURLINFO_HTTP_CODE);
    
    if($retcodeVacancy != 200){
        $vacanciesData = null;
    }else{
        $vacanciesData = json_decode($responseVacancy);
    }
    curl_close($curlVacancies);
    
?>



<?php 
    //PAGINAÇÃO
    $totalPages = 1;

    function pageTitle() { 
        global $companyName;
        return "$companyName - Vagas Disponíveis";
    }
    add_filter( 'pre_get_document_title', 'pageTitle', 999 );


    add_action('wp_head', function(){
        global $companyLogoUrl, $companyDescription;
        echo "
        <meta property='og:description' content='$companyDescription'/>
        <meta property='og:image'content='$companyLogoUrl'/>
        <meta property='og:image:width'content='1200'/>
        <meta property='og:image:height'content='630'/>
        ";
    });
   
    get_header();
    
?>


<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/templates/styles.css" type="text/css" />
<script src="https://kit.fontawesome.com/85921d822d.js" crossorigin="anonymous"></script>

<section class="company__header">
    <div class="container">
        <div class="company__header_content">
            <div class="company__header_logo">
                <img src="<?php echo $companyData[0] -> foto ? 'data:image/jpg;base64,' . $companyData[0] -> foto : 'https://bantal.com.br/wp-content/uploads/2022/07/cropped-bantal-favicon.png'; ?>" alt="logo-da-empresa" />
            </div>

            <div class="company__header_info_wrapper">
                <div class="company__header_info">
                    <h1 class="company__header_info_title"><?php echo $companyData[0] -> nomeEmpresa; ?></h1>

                    <div class="company__header_info_subtitle">
                        <span>Total de vagas cadastradas: <strong class="company__vacancies"><?php echo $companyData[0] -> quantidade; ?></strong></span>
                    </div>
                </div>

                <div class="company__header_info_about">
                    <h2>Sobre a empresa</h2>
                    <p><?php echo $companyData[0] -> objetivo; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="company__vancancies">
    <div class="container">
        <div class="company__vancacies_header">
            <h3 class="section__title">Vagas abertas no momento</h3>

            <!-- <div class="searchForm__wrapper">
                <input type="text" value="" placeholder="Pesquisar vagas" />
                <button class="searchForm__button"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div> -->
        </div>

        <div class="vacancy__wrapper">
            <?php if($vacanciesData): ?>
                <?php foreach($vacanciesData as $vacancy){ 
                    $vacancyId = $vacancy -> idVaga;
                    ?>
                <div class="vacancy__card">
                    <h4 class="vacancy__title"><?php echo $vacancy -> tituloVaga;?></h4>
                    <div class="vacancy__metaData">
                        <span class="vacancy__metaData_date"><i class="fa-solid fa-calendar"></i> <?php echo $vacancy -> dataVaga; ?> </span>
                        <span class="vacancy__metaData_localization"><i class="fa-solid fa-location-dot"></i> <?php echo $vacancy -> localizacaoVaga; ?> </span>
                        <span class="vacancy__metaData_localization"><i class="fa-solid fa-suitcase"></i> <?php echo $vacancy -> nomeEmpresa; ?> </span>
                    </div>

                    <div class="btn__primary">
                        <a class="btn__link" href=<?php echo "$vacancyPage/$companySlug?vaga_id=$vacancyId"; ?>>Ver vaga</a>
                    </div>
                </div>

            <?php } ?>

            <?php else:?>
                <span><b>NENHUMA VAGA ENCONTRADA!</b></span>
            <?php endif; ?>
            
        </div>

        <?php if($totalPages > 1): ?>
            <div class="pagination__wrapper">
                <div class="pagination__buttons_wrapper">
                    
                    <?php if($currentPage !== 1): ?>
                        <a href="<?php echo $prevPage; ?>"  class="prev__page">Página Anterior</a>
                    <?php endif; ?>


                    <?php if($currentPage !== $totalPages): ?>
                       <a href="<?php echo $nextPage; ?>"  class="next__page">Próxima Página</a>
                    <?php endif; ?>
                </div>

                <div class="pagination__input_wrapper">
                    <span>Página</span>
                    <input type="number" placeholder=<?php echo $currentPage; ?> />
                    <span>de 23</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>



<script>
    // const searchForm = document.querySelector('.searchForm__wrapper input')
    // const searchButton = document.querySelector(".searchForm__button")

    // searchButton.addEventListener("click", getSearchTerm)
    // searchForm.addEventListener("keypress", function(e){
    //      if(e.key === "Enter"){
    //         getSearchTerm()
    //     }
    // })

    // let searchTerm = ""

    // function getSearchTerm(e){
    //     searchTerm = searchForm.value
    //     console.log('searchTerm:', searchTerm)
    // }

    // const pageNumber = document.querySelector(".pagination__input_wrapper input");
    // pageNumber.addEventListener("keypress", goToPageNumber)

    // function goToPageNumber(e){
    //     if(e.key === "Enter"){
    //         location.href = `<?php echo $siteUrl . "/vagas/" . $companySlug . "/?page="?>${pageNumber.value}`
    //     }        
    // }
</script>

<?php get_footer(); ?>