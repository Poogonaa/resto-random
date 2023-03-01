import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['sort', 'table'];

    connect() {
        this.element
    }

    sort(event){
        this.sortTargets.forEach((element) => {
            element.classList.remove('fa-sort-up');
            element.classList.remove('fa-sort-down');
        });

        (function (n, table) {
            let rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            switching = true;
            dir = "asc";
            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    if (dir === "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                        event.currentTarget.classList.remove('fa-sort-down');
                        event.currentTarget.classList.add('fa-sort-up');
                    } else if (dir === "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                        event.currentTarget.classList.remove('fa-sort-up');
                        event.currentTarget.classList.add('fa-sort-down');
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount ++;
                } else {
                    if (switchcount === 0 && dir === "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        })(event.currentTarget.dataset.sort, this.element);
    }
}