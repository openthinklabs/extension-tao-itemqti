export default {
    deleteItem: '[data-context="instance"][data-action="deleteItem"]',
    deleteClass: '[data-context="class"][data-action="deleteItemClass"]',
    newClass: '[data-context="resource"][data-action="subClass"]',
    addItem: '[data-context="resource"][data-action="instanciate"]',
    itemForm: 'form[action="/taoItems/Items/editItem"]',
    itemClassForm: 'form[action="/taoItems/Items/editClassLabel"]',
    editClass: '#item-class-schema',
    classForm: 'form[data-action= "/taoItems/Items/editItemClass"]',
    deleteConfirm: '[data-control="delete"]',
    root: '[data-uri="http://www.tao.lu/Ontologies/TAOItem.rdf#Item"]',
    authoring: '[data-context="instance"][data-action="launchEditor"]'
};
