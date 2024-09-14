/* global Backbone, _, $ */

import {LineTax} from "../models/line-tax";

export const LineTaxes = Backbone.Collection.extend({
	model: LineTax,
});

export default LineTaxes;
