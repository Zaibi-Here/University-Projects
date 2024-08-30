import React from 'react';
import {Box} from "@mui/material";
import Header from 'components/Header';
import BreakDownChart from "components/breakdown";


const BreakDown = () => {
  return (
   <Box m="1.5rem 2.5rem">
      <Header title="BREAKDOWN" subTitle="Breakdown of sales by category"/>
     <Box mt="40px" height="73vh" >
            <BreakDownChart/>
     </Box>  
 
    </Box>
  )
}

export default BreakDown