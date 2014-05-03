package com.megasoft.entangle;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class RequestEntryFragment extends Fragment {
	
	private View view;
	
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState){
		this.view = inflater.inflate(R.layout.request_entry_fragment, container,false);
		
		setAttributes();
		
		return this.view;
	}

	private void setAttributes() {
		Bundle args = getArguments();
		((TextView)view.findViewById(R.id.request_entry_description)).setText(args.getString("description"));
		((TextView)view.findViewById(R.id.request_entry_requester_name)).setText(args.getString("requesterName"));
		((TextView)view.findViewById(R.id.request_entry_date)).setText("Created at" + args.getString("date"));
		((TextView)view.findViewById(R.id.request_entry_tags)).setText(args.getString("tags"));
		((TextView)view.findViewById(R.id.request_entry_price)).setText(args.getString("price"));
		((TextView)view.findViewById(R.id.request_entry_deadline)).setText(args.getString("deadline"));
		((TextView)view.findViewById(R.id.request_entry_status)).setText(args.getString("status"));
	}
}
