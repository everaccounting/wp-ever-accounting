import { DocumentItem } from './models';

export const DocumentItems = Backbone.Collection.extend( {
	model: DocumentItem,
} );


export default Collection => {
	Collection.DocumentItems = DocumentItems;
}
