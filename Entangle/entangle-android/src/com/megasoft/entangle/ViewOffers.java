package com.megasoft.entangle;


import android.app.Activity;
import android.os.Bundle;

public class ViewOffers extends Activity {
	public String[][] offerDetails; 

	protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.all_offers);
        Bundle bundle = getIntent().getExtras();
        offerDetails = (String[][])bundle.getSerializable("offers");	
	}

	
}


