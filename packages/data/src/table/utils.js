import {xor} from "lodash";

/**
 * set if not exist remove if exist
 * @param meta
 * @param id
 * @returns {{selected: *}}
 */
export const setSelected = (meta, id) => ({ ...meta, selected: xor(meta.selected, parseInt(id, 10)) });
