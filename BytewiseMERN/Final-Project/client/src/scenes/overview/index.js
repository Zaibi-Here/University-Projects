import React, { useState } from "react";
import Header from "components/Header";
import { FormControl, MenuItem, InputLabel, Box, Select } from "@mui/material";
import OverviewChart from "../../components/overviewChart";

const Overview = () => {
  const [view, setView] = useState("units");

  return (
    <Box m="1.5rem 2.5rem">
      <Header
        title="OVERVIEW"
        subTitle="Overview Of General Revenue and Profit"
      />
      <Box sx={{ height: "72vh" }}>
        <FormControl sx={{mt:"1rem"}}>
           <InputLabel>View</InputLabel> 
           <Select
           value={view}
           label="View"
           onChange = {(event)=>setView(event.target.value)}
           >
           <MenuItem value="sales" >Sales</MenuItem>
           <MenuItem value="units" >Units</MenuItem>
          </Select>
        </FormControl>
      <OverviewChart view={view}/>
    </Box>
    </Box>
  );
};

export default Overview;
