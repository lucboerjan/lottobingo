const MIJNFUNCTIES = {
    //datum omzetten naar dd-mm-jjjj
    omzettenDatum: (dtm) => {
        if (dtm) {
            let tmp = dtm.split('-');
            tmp.reverse()
            return tmp.join('-');
        }
        else {
            return '-';
        }

    }
}