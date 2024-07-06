<?php
ini_set('display_errors', 1);
    $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $siteUrl = get_site_Url();
    $companySlug = get_query_var( 'empresa_slug' ); 
    $vacancyId = get_query_var( 'vaga_id' ); 
    $socialMediaLinks = array("Facebook"=>"https://www.facebook.com/sharer.php?u=$actual_link - ", "Linkedin"=>"https://www.linkedin.com/sharing/share-offsite/?url=$actual_link - ", "Twitter"=>"https://twitter.com/share?url=$actual_link&text=", "Whatsapp"=>"https://wa.me/?text=$actual_link", "Telegram"=>"https://telegram.me/share/url?url=$actual_link&text=");
    $vacancyPage = $siteUrl . "/empresa/" . $companySlug;

    //API DO SISTEMA
    $BASEURL = "https://www.spring.bantal.com.br/api/publico/";

    function pageTitle() {    
        return "Bantal - Detalhes da Vaga";
    }
    add_filter( 'pre_get_document_title', 'pageTitle', 999 );

    function addMetaTagsToHead(){
        $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        echo '<meta property="og:url" content="' . $actual_link . '" />
            <meta property="og:type" content="website" />
            <meta id="og_title" property="og:title" content="Bantal - Banco de Talentos" />
            <meta property="og:description" content="BANTAL é uma empresa brasileira de recrutamento de pessoas. Com o objetivo principal de facilitar contratações." />
            <meta property="og:image" content="https://bantal.com.br/wp-content/uploads/2023/06/bantal-logo-cor_share-1.jpg" />';
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
                    <h4 class="vacancy__title"></h4>
                    <div class="vacancy__metaData">
                        <div class="vacancy__metaData_item"><i class="fa-solid fa-suitcase"></i> <span class="vacancy__area"></span></div>
                        <div class="vacancy__metaData_item"><i class="fa-solid fa-calendar"></i> <span class="vacancy__date"> </span></div>
                        <div class="vacancy__metaData_item"><i class="fa-solid fa-location-dot"></i> <span class="vacancy__local"> </span></div>
                    </div>
                </div>

                <div class="vacancy__details">
                    <h2 class="section__title">Descrição da Vaga</h2>

                    <div class="vacancy__description"></div>

                </div>

                  <div class="socialMenu__wrapper">
                        <span>Compartilhe essa vaga</span>
                        <div class="socialMenu__buttons_wrapper">
                            <a target="_blank" href="" class="facebook__link"><i class="fa-brands fa-facebook"></i></a>
                            <a target="_blank" href="" class="linkedin__link"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a target="_blank" href="" class="twitter__link"><i class="fa-brands fa-twitter"></i></a>
                            <a target="_blank" href="" class="email__link"><i class="fa-solid fa-envelope"></i></a>
                            <a target="_blank" href="" class="whatsapp__link"><i class="fa-brands fa-whatsapp"></i></a>
                            <a target="_blank" href="" class="telegram__link"><i class="fa-brands fa-telegram"></i></a>
                            <a href="#" class="clipBoard__button">
                                <i class="fa-solid fa-copy"></i>
                                Copiar Link
                            </a>
                        </div>
                    </div>

                    <div class="btn__primary">
                        <a class="btn__link" href="<?php echo $siteUrl; ?>/carrinho/?add-to-cart=2418">Quero me candidatar</a>
                    </div>
            </div>

            <aside class="sidebar">
                <div class="company__sumary">
                    <div class="company__header_logo">
                        <img src="https://bantal.com.br/wp-content/uploads/2022/07/cropped-bantal-favicon.png" alt="logo-da-empresa" />
                    </div>

                    <div class="company__sumary_card">
                        <div class="company_card">
                            <h4 class="company_card_title"></h4>

                            <div class="company_card_subtitle">
                                <span>Vagas: <strong class="company__vacancies"></strong></span>
                            </div>

                            <div class="btn__primary">
                                <a class="btn__link company__profile_btn" href="">Ver Perfil</a>
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
            <div class="vacancy__carousel"></div>
        </div>  
    </div>
</section>

<input id="hidden_url" type="text" value="<?php echo $actual_link; ?>" style="display: none;" />

<script>
    const getCompanyData = async() => {        
        try{
            const req = await fetch(`<?php echo $BASEURL . "empresas/sigla/$companySlug";?>`)
            const res = await req.json()
            console.log(req)
            document.querySelector(".company_card_title").innerText = `${res[0].nomeEmpresa}`
            document.querySelector(".company__vacancies").innerText = `${res[0].quantidade}`  
            document.querySelector(".company__profile_btn").href =  "<?php echo $siteUrl . '/empresa/' . $companySlug; ?>"
            document.querySelector(".company__header_logo img").src = `data:image/jpg; base64, ${res[0].foto}`
        }catch(error){
            document.querySelector(".company__vacancies").innerText = 0         
        }
    }
    

    const getVacancyDetails = async() => {
        try{
            const req = await fetch("<?php echo $BASEURL . "/vagas-disponives/detalhamento/" . $vacancyId?>")
            const res = await req.json()
            const shareText = `${res[0].nomeEmpresa} - Vaga para ${res[0].tituloVaga} - ${res[0].localizacaoVaga}`

            document.querySelector(".vacancy__title").innerText = `${res[0].tituloVaga}`
            document.querySelector(".vacancy__area").innerText = `${res[0].areaAtuacao}`
            document.querySelector(".vacancy__date").innerText = `${res[0].dataVaga}`
            document.querySelector(".vacancy__local").innerText = `${res[0].localizacaoVaga}`
            document.querySelector(".vacancy__description").innerHTML = `${res[0].descricaoVaga}`

            document.querySelector(".email__link").href = `mailto:?subject=${shareText}&body=Link - <?php echo $actual_link; ?>`
            document.querySelector(".facebook__link").href = `<?php echo $socialMediaLinks["Facebook"]; ?>`            
            document.querySelector(".linkedin__link").href = `<?php echo $socialMediaLinks["Linkedin"]; ?>`
            document.querySelector(".twitter__link").href = `<?php echo $socialMediaLinks["Twitter"]; ?>${shareText}`
            document.querySelector(".whatsapp__link").href = `<?php echo $socialMediaLinks["Whatsapp"]; ?> - ${shareText}`
            document.querySelector(".telegram__link").href = `<?php echo $socialMediaLinks["Telegram"]; ?> ${shareText}`

            const ogTitle = `Vaga: ${res[0].tituloVaga}`  

            const title = document.querySelector("#og_title")
            title.content = ogTitle

            getCompanyData()
            getVacancies(res[0].userId)


        }catch(error){
            console.log(error)
            location.href = "/404"
        }
    }

  
    const getVacancies = async(companyId) => {
        try{
            const req = await fetch(`<?php echo $BASEURL . "/vagas-disponives/empresa/";?>/${companyId}`)
            const res = await req.json()

            const vacancies = res.map((item) => {
                return(
                    `
                    <div class="vacancy__carousel_card">
                        <h4 class="vacancy__title">${item.tituloVaga}</h4>
                        <div class="vacancy__metaData">
                                <span><i class="fa-solid fa-calendar"></i> ${item.dataVaga}</span>
                            <span><i class="fa-solid fa-location-dot"></i> ${item.localizacaoVaga}</span>
                        </div>

                        <div class="btn__primary">
                            <a class="btn__link" href=<?php echo $vacancyPage; ?>?vaga_id=${item.idVaga}>Ver vaga</a>
                        </div>
                    </div>
                    `
                )
            }).join("")                

            document.querySelector(".vacancy__carousel").innerHTML = vacancies
            slickCarousel()
            
        }catch(error){
            console.log(error)
        }

    }

    getVacancyDetails()
    

</script>



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
    
    

    const slickCarousel = () => {
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
                    breakpoint: 1024,
                    settings: {
                    slidesToShow: 6,
                    infinite: true,
                    },
                },
                {
                    breakpoint: 600,
                    settings: {
                    slidesToShow: 3,
                    dots: true,
                    },
                },
                {
                    breakpoint: 300,
                    settings: "unslick",
                },
                ],
            });
            });
    }
</script>


<?php get_footer() ?>