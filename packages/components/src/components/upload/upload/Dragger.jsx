import * as React from 'react';
import Upload from './Upload';
const Dragger = React.forwardRef(({ style, height, ...restProps }, ref) => (<Upload ref={ref} {...restProps} type="drag" style={{ ...style, height }}/>));
if (process.env.NODE_ENV !== 'production') {
    Dragger.displayName = 'Dragger';
}
export default Dragger;
