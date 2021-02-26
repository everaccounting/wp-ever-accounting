import $ from "jquery" ;
import {blockUI} from "@eaccounting/utils";

$(document).ready(function () {
	blockUI({el: '.ea-cash-flow'});
	blockUI({el: '#ea-latest-income'});
});
