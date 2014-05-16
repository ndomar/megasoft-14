package com.megasoft.entangle;

import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class ClaimEntryFragment extends Fragment {

	private int claimId;
	
	private View view;
	
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState){
		this.view = inflater.inflate(R.layout.claim_entry_fragment, container,false);
		view.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
//			//	Intent intent = new Intent(getActivity(), ClaimActivity.class);
//				intent.putExtra("claimId", claimId);
//				startActivity(intent);
//				
			}
		});
		setAttributes();
		
		return this.view;
	}

	private void setAttributes() {
		Bundle args = getArguments();
		((TextView)view.findViewById(R.id.claim_entry_id)).setText(args.getString("claim"));
		((TextView)view.findViewById(R.id.claim_entry_value)).setText(args.getString("price"));
		this.claimId = args.getInt("claimId");
	}

}
