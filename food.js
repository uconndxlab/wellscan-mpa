class Food {
    constructor(upc, name, rankings, nutrition, nutrition_method, nutrition_source) {
      this.upc = upc;
      this.name = name;
      this.rankings = rankings;
      this.nutrition = nutrition;
      this.nutrition_method = nutrition_method;
      this.nutrition_source = nutrition_source;
    }
  
    template(where, data){
      
      return where.innerHTML
        .replace(
          /{(\w*)}/g, // or /{(\w*)}/g for "{this} instead of %this%"
          function( m, key ){
            return data.hasOwnProperty( key ) ? data[ key ] : "";
          }
        );
    }
  
    display(where) {
      let that = this;
      let rank;
      let category;
      // #TODO move this ranking stuff to the constructor
      if(typeof that.rankings.swap !== 'undefined') {
          rank = that.rankings.swap.rank;
          category = that.rankings.swap.category;
          switch(rank) {
              case "rarely":
                  rank = {value:1, rank: "rarely"}
              break;
  
              case "sometimes":
                  rank = {value:2, rank: "sometimes"}
              break;
  
              case "often":
                  rank = {value:3, rank:"often"}
              break;
          }
      }
      else {
          rank = {value:0, rank:"unranked"}
      }
      this.rank=rank;
      this.category = category;
  
      where.innerHTML = this.template(where, {
          name:that.name, 
          upc:that.upc, 
          nf_saturated_fat:that.nutrition.nf_saturated_fat, 
          nf_sodium:that.nutrition.nf_sodium, 
          nf_sugars:that.nutrition.nf_sugars,
          nutrition_source: that.nutrition_source,
          nutrition_method: that.nutrition_method,
          rank: rank,
          category: category
      } )
    }
  }