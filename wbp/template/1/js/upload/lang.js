var wbp_photo_competition = {
    pl: {
        basic: 'Dane podstawowe',
        name: 'Imię',
        photos: 'Zdjęcia zgłoszone do konkursu',
        surname: 'Nazwisko',
        address: 'Ulica',
        number: 'Nr domu/mieszkania',
        city: 'Miasto',
        postal: 'Kod pocztowy',
        country: 'Kraj',
        phone: 'Telefon',
        email: 'E-mail',
        photo_title: 'Tytuł zdjęcia',
        set_title: 'Tytuł zestawu',
        setno: 'Nr w zestawie',
        photoset: 'Zdjęcie w zestawie',
        add: 'Dodaj zdjęcia...',
        save: 'Wyślij',
        description: 'Opis zdjęcia',
        choose_cat: 'Wybierz kategorię',
        events: 'Wydarzenia',
        human_passion: 'Człowiek i jego pasje',
        life:'Życie codzienne',
        sport:'Sport',
        error: 'Nie wszystkie pola formularza zostały wypełnione lub nie wgrano minimalnej liczby zdjęć',
        environment:'Przyroda i ekeologia',
        agreeauthor: 'Oświadczam, że jestem autorem w/w zdjęć, a sportretowane osoby wyraziły pisemną zgodę na ich publikację w prasie.',
        agreeterms: 'Akceptuję warunki regulaminu, w tym zgodę na bezpłatną reprodukcję zdjęć w katalogu, w prasie, telewizji i internecie oraz na stronie Wielkopolskiej Biblioteki Cyfrowej',
        agreepublish: 'Wyrażam zgodę na publikowanie moich danych osobowych zgodnie z ustawą z dnia 29.08.1997 r. o ochronie danych osobowych (t. j. Dz. U. z 2002r Nr 101 poz. 926 z póź. zm.)',
        agreemarketing: 'Wyrażam zgodę na przetwarzanie moich danych osobowych przez WBPiCAK w celach promocyjnych. oraz na otrzymywanie od WBPiCAK na podany przeze mnie adres email. informacji o organizowanych imprezach kulturalnych. Mam prawo wglądu i poprawiania swoich danych.',
        minNumberOfFiles: 'Za mało zdjęć',
        maxNumberOfFiles: 'Przekroczono maksymalną liczbę plików',
        acceptFileTypes: 'Niedozwolony typ pliku',
        maxFileSize: 'Plik jest za duży',
        minFileSize: 'Plik jest za mały',
        loading: 'Ładuję ...',
        payment: 'Płatność',
        discard: 'Odrzuć',
        pay: 'Pomiń zdjęcia z, których nie udało się wgrać i zakończ przesyłanie, te, których nie udało się wgrać prosimy wysłać na adres e-mail: foto@wbp.poznan.pl',
        uploaderr: 'Niestety przy wgrywaniu niektórych zdjęć pojawiły się błędy, prosimy je dodać ponownie'
    },
    en: {
        basic: 'Basic data',
        name: 'Name',
        photos: 'Photos submitted to the contest',        
        surname: 'Surname',
        address: 'Street',
        number: 'Building No.',
        city: 'City',
        postal: 'Postal',
        country: 'Country',
        phone: 'Phone',
        email: 'E-mail',
        photo_title: 'Photo title',
        set_title: 'Collection title',
        setno: 'Order in collection',
        photoset: 'Image in collection',
        add: 'Add photos...',
        save: 'Send',
        description: 'Image description',
        choose_cat: 'Choose category',
        events: 'Events',
        human_passion: 'Man and his passions',
        life:'Life',
        sport:'Sport',
        environment:'Environment',
        error: 'Not all required fields have been filled or you haven\'t uploaded the minimum number of images',
        agreeauthor: 'I declare that I am the author of the above mentioned photos and that people portrayed therein have granted their permission for photos to be published in press.',
        agreeterms: 'I accept terms and conditions set out in the contest regulations and I grant my permission for my personal data to be published in line with Personal Data Protection Law dated 28 August 1997 (Journal of Laws, issue133, item 883.)',
        agreepublish: 'In order to popularize the event, the organizer reserves the right to free photo reproduction for the use in the catalog and press, on TV and the Internet, also on the Digital Library of Wielkopolska',
        agreemarketing: 'Wyrażam zgodę na przetwarzanie moich danych osobowych przez WBPiCAK w celach promocyjnych. oraz na otrzymywanie od WBPiCAK na podany przeze mnie adres email. informacji o organizowanych imprezach kulturalnych. Mam prawo wglądu i poprawiania swoich danych.',
        maxNumberOfFiles: 'Maximum number of files exceeded',
        minNumberOfFiles: 'Too little of images',
        acceptFileTypes: 'File type not allowed',
        maxFileSize: 'File is too large',
        minFileSize: 'File is too small',
        loading: 'Loading ...',
        payment: 'Payment',
        discard: 'Discard',
        pay: 'Ignore failed images, just finish my process, photos that failed to upload please send us to foto@wbp.poznan.pl',
        uploaderr: 'Unfortunately some of images failed to upload, please check and try to add them again'
    }
}

var wbp_photo_lang;

function apply_wbp_lang()
{
    var lang=wbp_photo_lang;

    $('.contest-lang').hide();
    $('.contest-'+lang).show();
    
    $('#fileupload,#foto-contest-payment').find('label').each(function() {
        var f=$(this).attr('for');
        var c=$(this).attr('class');
        var html=wbp_photo_competition[lang][f]?wbp_photo_competition[lang][f]:wbp_photo_competition[lang][c];
        $(this).html(html);    
    });
    $('#fileupload').find('option').each(function() {
        var c=$(this).attr('class');
        $(this).html(wbp_photo_competition[lang][c]);    
    });
    
    $('#fileupload').find('input.placeholder, textarea.placeholder').each(function() {
        var c=$(this).attr('class').replace('placeholder','').replace('required','').replace('error','').trim();
        $(this).attr("placeholder",wbp_photo_competition[lang][c]);
    });
    
        
    $('#fileupload').find('[title]').each(function() {
        var title=$(this).attr('title');
    
        if (typeof(wbp_photo_competition[lang][title])=='string') {
            $(this).attr('title',wbp_photo_competition[lang][title]);
        }
    
    });
    
    $('#fileupload').find('.text-danger').each(function() {
        if (lang=='pl') {
            for (var key in wbp_photo_competition.en) {
                if (wbp_photo_competition.en[key] == $(this).html()) {
                    $(this).html(wbp_photo_competition.pl[key]);
                }
            }
        }
        if (lang=='en') {
            for (var key in wbp_photo_competition.pl) {
                if (wbp_photo_competition.pl[key] == $(this).html()) {
                    $(this).html(wbp_photo_competition.en[key]);
                }
            }
        }        
    });
}

function switch_wbp_lang(lang)
{
    if (typeof(lang)!='string') lang = wbp_photo_lang=='pl'?'en':'pl';
    wbp_photo_lang=lang;
    
    var src=$('#lang_selector_img').attr('src');
    src=src.replace('-pl.gif','-'+lang+'.gif')
    src=src.replace('-en.gif','-'+lang+'.gif')
    $('#lang_selector_img').attr('src',src);
    
    apply_wbp_lang();

}
