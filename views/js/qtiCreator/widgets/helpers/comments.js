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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 */
define(function () {
    'use strict';

    const autogeneratedCodeStartCommentText = ' autogenerated code, do not edit ';
    const autogeneratedCodeEndCommentText = ' end of autogenerated code ';
    const autogeneratedCodeStartComment = `<!--${autogeneratedCodeStartCommentText}-->`;
    const autogeneratedCodeEndComment = `<!--${autogeneratedCodeEndCommentText}-->`;

    const isCommentNode = (node, text) =>
        node &&
        node.nodeType === Node.COMMENT_NODE &&
        node.textContent &&
        decodeURIComponent(node.textContent).includes(text);

    const findAutogeneratedCodeCommentSibling = (el, siblingDirection) => {
        let commentNode = null;
        const siblingNode = el[siblingDirection];
        const commentsByDirection = {
            previousSibling: autogeneratedCodeStartCommentText,
            nextSibling: autogeneratedCodeEndCommentText
        };

        if (!siblingNode) {
            return null;
        }

        if (siblingNode.textContent && siblingNode.textContent.trim().length === 0) {
            commentNode = findAutogeneratedCodeCommentSibling(siblingNode, siblingDirection);
        } else {
            //ckeditor  wraps comments and URIEncodes it
            //so that is the reason to use decodeURIComponent
            if (isCommentNode(siblingNode, commentsByDirection[siblingDirection])) {
                commentNode = siblingNode;
            }
        }
        return commentNode;
    };

    const isWrapped = $el =>
        findAutogeneratedCodeCommentSibling($el[0], 'previousSibling') &&
        findAutogeneratedCodeCommentSibling($el[0], 'nextSibling');

    return {
        wrapWithAutogeneratedComment: $el => {
            if (!isWrapped($el)) {
                $el.before(autogeneratedCodeStartComment);
                $el.after(autogeneratedCodeEndComment);
            }
        },

        removeAutogeneratedComments: (text) => {
            const replaceAll = (str, search, replacement) => str.replace(new RegExp(search, 'g'), replacement);
            text = replaceAll(text, autogeneratedCodeStartComment, '');
            text = replaceAll(text, autogeneratedCodeEndComment, '');
            return text;
        },
      
        removeAutogeneratedCommentNodes: el => {
            const prevCommentNode = findAutogeneratedCodeCommentSibling(el, 'previousSibling');
            const nextCommentNode = findAutogeneratedCodeCommentSibling(el, 'nextSibling');
            if (prevCommentNode) {
                prevCommentNode.remove();
            }
            if (nextCommentNode) {
                nextCommentNode.remove();
            }
        }
    };
});
