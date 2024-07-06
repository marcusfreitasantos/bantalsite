

<?php 
    $siteUrl = get_site_Url();
    $companySlug = get_query_var( 'empresa_slug' ); 
    $currentPage = get_query_var( 'page' ) ? get_query_var( 'page' ) : 1;
    $vacancyPage = $siteUrl . "/empresa";

    //API DO SISTEMA
    $BASEURL = "https://www.spring.bantal.com.br/api/publico/";

    //PAGINAÇÃO
    $totalPages = 1;

    function pageTitle() {                
        return "Bantal - Vagas Disponíveis";
    }
    add_filter( 'pre_get_document_title', 'pageTitle', 999 );

    get_header();
    
?>


<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/templates/styles.css" type="text/css" />
<script src="https://kit.fontawesome.com/85921d822d.js" crossorigin="anonymous"></script>

<section class="company__header">
    <div class="container">
        <div class="company__header_content">
            <div class="company__header_logo">
                <img src="https://bantal.com.br/wp-content/uploads/2022/07/cropped-bantal-favicon.png" alt="logo-da-empresa" />
            </div>

            <div class="company__header_info_wrapper">
                <div class="company__header_info">
                    <h1 class="company__header_info_title"></h1>

                    <div class="company__header_info_subtitle">
                        <span>Total de vagas cadastradas: <strong class="company__vacancies"></strong></span>
                    </div>
                </div>

                <div class="company__header_info_about">
                    <h2>Sobre a empresa</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque in dapibus elit, ornare pretium orci. Nulla dapibus, urna at convallis sodales, ex diam pellentesque orci, vitae maximus metus est at nibh. Ut sed finibus nisl. Proin cursus magna sit amet turpis luctus, in porta velit blandit. Maecenas dignissim sem sit amet ullamcorper accumsan. </p>
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

        <div class="vacancy__wrapper"></div>

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
    const searchForm = document.querySelector('.searchForm__wrapper input')
    const searchButton = document.querySelector(".searchForm__button")

    searchButton.addEventListener("click", getSearchTerm)
    searchForm.addEventListener("keypress", function(e){
         if(e.key === "Enter"){
            getSearchTerm()
        }
    })

    let searchTerm = ""

    function getSearchTerm(e){
        searchTerm = searchForm.value
        console.log('searchTerm:', searchTerm)
    }
</script>

<script>
    // const pageNumber = document.querySelector(".pagination__input_wrapper input");
    // pageNumber.addEventListener("keypress", goToPageNumber)

    // function goToPageNumber(e){
    //     if(e.key === "Enter"){
    //         location.href = `<?php echo $siteUrl . "/vagas/" . $companySlug . "/?page="?>${pageNumber.value}`
    //     }        
    // }
</script>

<script>
    const getCompanyData = async() => {        
        try{
            const req = await fetch("<?php echo $BASEURL . "empresas/sigla/" . $companySlug; ?>")
            const res = await req.json()  
            console.log(res)
            document.querySelector(".company__header_info_title").innerText = `${res[0].nomeEmpresa}`
            document.querySelector(".company__header_info_about p").innerText = `${res[0].objetivo}`
            document.querySelector(".company__vacancies").innerText = `${res[0].quantidade}`   
            document.querySelector(".company__header_logo img").src = `data:image/jpg;base64, ${res[0].foto}`  
            getVacancies(res[0].userId)

   
        }catch(error){
            console.log(error)
            location.href = "/404"     
        }
    }
    getCompanyData()

    const getVacancies = async(companyId) => {
        try{
            const req = await fetch(`<?php echo $BASEURL . "vagas-disponives/empresa/";?>${companyId}`)
            const res = await req.json()

            const vacancies = res.map((item) => {
                return(
                    `
                    <div class="vacancy__card">
                        <h4 class="vacancy__title">${item.tituloVaga}</h4>
                        <div class="vacancy__metaData">
                            <span class="vacancy__metaData_date"><i class="fa-solid fa-calendar"></i> ${item.dataVaga}</span>
                            <span class="vacancy__metaData_localization"><i class="fa-solid fa-location-dot"></i> ${item.localizacaoVaga} </span>
                            <span class="vacancy__metaData_localization"><i class="fa-solid fa-suitcase"></i> ${item.nomeEmpresa} </span>
                        </div>

                        <div class="btn__primary">
                            <a class="btn__link" href=<?php echo "$vacancyPage/$companySlug"; ?>?vaga_id=${item.idVaga}>Ver vaga</a>
                        </div>
                    </div>
                    `
                )
            }).join("")

            document.querySelector(".vacancy__wrapper").innerHTML = vacancies
        }catch(error){
            console.log(error)
            document.querySelector(".vacancy__wrapper").innerHTML = "<span><b>NENHUMA VAGA ENCONTRADA!</b></span>"
        }
    }

</script>

<?php get_footer(); ?>
<?php
    $companySlug = get_query_var( 'empresa_slug' ); 
    $curlCompany = curl_init();
    curl_setopt_array($curlCompany, array(
    
    CURLOPT_URL => '172.16.1.2:8080/api/publico/empresas/sigla/' . $companySlug,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_IPRESOLVE,
    CURL_IPRESOLVE_V4
    ));


    $response = curl_exec($curlCompany);

    print_r($response);

    //$retcode = curl_getinfo($curlCompany, CURLINFO_HTTP_CODE);

    //RETORNA STATUS 404 QUANDO A EMPRESA NÃO FOR ENCONTRADA
    // if($retcode != 200){
    //     global $wp_query;
    //     $wp_query->set_404();
    //     status_header( 404 );
    //     get_template_part( 404 );
    //     exit();
    // }

    if (curl_errno($curlCompany)) {
       echo $error_msg = curl_error($curlCompany);
    }

    curl_close($curlCompany);
    // $companyData = json_decode($response);
    // $companyId = $companyData[0] -> userId;

?>