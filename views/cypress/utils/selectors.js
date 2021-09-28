export default {
    addItem: '[data-context="resource"][data-action="instanciate"]',
    authoring: '[data-context="instance"][data-action="launchEditor"]',
    addSubClassUrl: 'taoItems/Items/addSubClass',

    deleteClass: '[data-context="class"][data-action="deleteItemClass"]',
    deleteConfirm: '[data-control="delete"]',
    deleteClassUrl: 'taoItems/Items/deleteClass',

    editItemUrl: 'taoItems/Items/editItem',
    editClassLabelUrl: 'taoItems/Items/editClassLabel',

    itemForm: 'form[action="/taoItems/Items/editItem"]',
    itemClassForm: 'form[action="/taoItems/Items/editClassLabel"]',

    root: '[data-uri="http://www.tao.lu/Ontologies/TAOItem.rdf#Item"]',
    resourceRelations: 'tao/ResourceRelations',

    treeRenderUrl: 'taoItems/Items',
};
