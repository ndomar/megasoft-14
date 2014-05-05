package com.megasoft.entangle;

import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class OfferEntryFragment extends Fragment{
	
	private int offerId;
	
	private View view;
	
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState){
		this.view = inflater.inflate(R.layout.offer_entry_fragment, container,false);
		view.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity(), OfferActivity.class);
				intent.putExtra("offerID", offerId);
				
			}
		});
		setAttributes();
		
		return this.view;
	}

	private void setAttributes() {
		Bundle args = getArguments();
		((TextView)view.findViewById(R.id.offer_entry_requested_price)).setText(args.getString("requestedPrice"));
		((TextView)view.findViewById(R.id.offer_entry_date)).setText(args.getString("date"));
		((TextView)view.findViewById(R.id.offer_entry_description)).setText(args.getString("description"));
		((TextView)view.findViewById(R.id.offer_entry_offerer)).setText(args.getString("offerer"));
		((TextView)view.findViewById(R.id.offer_entry_status)).setText(args.getString("status"));
		
		this.offerId = args.getInt("offerId");
	}
}
