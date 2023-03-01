import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["recherche", "table"];

    search(){
        let filtre, tableau, ligne, cellules, i, j, affichage;
        filtre = this.rechercheTarget.value.toUpperCase();
        tableau = this.tableTarget;
        ligne = tableau.getElementsByTagName("tr");

        for(i=0; i<ligne.length; i++){
            cellules = ligne[i].getElementsByTagName("td");
            if(cellules.length > 0){
                affichage = false;
                for(j=0; j<cellules.length-1; j++){
                    if(cellules[j].innerHTML.toUpperCase().indexOf(filtre) > -1){
                        affichage = true;
                    }
                }
                if(affichage){
                    ligne[i].style.display = "";
                }
                else{
                    ligne[i].style.display = "none";
                }
                affichage = false;
            }
        }
    }
}