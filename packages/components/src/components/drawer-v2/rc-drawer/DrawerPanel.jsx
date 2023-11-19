import classNames from 'classnames';
import {useComposeRef} from 'rc-util';
import * as React from 'react';
import {RefContext} from './context';

const DrawerPanel = (props) => {
    const {
        prefixCls,
        className,
        style,
        children,
        containerRef,
        id,
        onMouseEnter,
        onMouseOver,
        onMouseLeave,
        onClick,
        onKeyDown,
        onKeyUp,
    } = props;
    const eventHandlers = {
        onMouseEnter,
        onMouseOver,
        onMouseLeave,
        onClick,
        onKeyDown,
        onKeyUp,
    };
    const {panel: panelRef} = React.useContext(RefContext);
    const mergedRef = useComposeRef(panelRef, containerRef);
    // =============================== Render ===============================
    return (<>
        <div id={id} className={classNames(`${prefixCls}-content`, className)} style={{
            ...style,
        }} aria-modal="true" role="dialog" ref={mergedRef} {...eventHandlers}>
            {children}
        </div>
    </>);
};
if (process.env.NODE_ENV !== 'production') {
    DrawerPanel.displayName = 'DrawerPanel';
}
export default DrawerPanel;
