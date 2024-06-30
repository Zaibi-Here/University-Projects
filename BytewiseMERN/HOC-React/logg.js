import React from 'react';

const logg = (WrappedComponent) => {
  return class extends React.Component {
    componentDidUpdate(prevProps) {
      console.log('Current props: ', this.props);
      console.log('Previous props: ', prevProps);
    }

    render() {
      return <WrappedComponent {...this.props} />;
    }
  };
};

export default logg;
