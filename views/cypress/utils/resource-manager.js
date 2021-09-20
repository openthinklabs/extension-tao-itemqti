/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2021 Open Assessment Technologies SA ;
 */

/**
 * Select file in resource manager or upload it
 * @param {String} fileName
 * @param {String} pathToFile
 */
export function selectUploadLocalAsset(fileName, pathToFile) {
    cy.log('SELECT OR UPLOAD LOCAL ASSET', 'add image option if needed');
    cy.get('.resourcemgr.modal')
        .last()
        .then(resourcemgr => {
            const resourcemgrId = resourcemgr[0].id;
            cy.getSettled(`#${resourcemgrId} .file-browser .root-folder`).should('have.length', 2);
            cy.getSettled(`#${resourcemgrId} .file-browser .local .root-folder`).click();
            cy.get(`#${resourcemgrId} .file-selector .files`).then(root => {
                if (root.find(`#${resourcemgrId} li[data-file="/${fileName}"]`).length === 0) {
                    cy.getSettled(`#${resourcemgrId} .file-selector .upload-switcher .upload`).click();
                    cy.fileUpload(
                        `#${resourcemgrId} .file-upload-container input[type="file"][name="content"]`,
                        pathToFile
                    );
                    cy.getSettled(`#${resourcemgrId} .file-upload-container .btn-upload`).click();
                    cy.getSettled(`#${resourcemgrId} .file-upload-container .progressbar.success`).should('exist');
                }
            });
            cy.getSettled(`#${resourcemgrId} li[data-file="/${fileName}"] .actions a.select`).click();
        });
}
