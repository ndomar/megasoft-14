package com.megasoft.entangle.viewtanglelsit;

import android.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;

import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.entangle.TangleActivity;


public class TangleEntryFragment extends Fragment {

	private int tangleId;
	
	private String text;
	
	private View layout;
	
	private Button button;
	
	private String sessionId;
	
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		layout = inflater.inflate(R.layout.tangle_entry_fragment,container, false);
		this.sessionId = getActivity().getSharedPreferences(Config.SETTING, 0).getString(Config.SESSION_ID, "");
		return layout;
	}

	public void onStart() {
		super.onStart();
		
		button = (Button) layout.findViewById(R.id.view_tangle_tangle_entry);
		button.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity(),TangleActivity.class);
				intent.putExtra("tangleId", tangleId);
				intent.putExtra("tangleName", text);
				intent.putExtra("sessionId", sessionId);
				startActivity(intent);
			}
		});
		
		button.setText(text);
		
	}
	
	
	public static TangleEntryFragment createInstance(int tangleId,String text) {
		TangleEntryFragment fragment = new TangleEntryFragment();
		fragment.setTangleId(tangleId);
		fragment.setText(text);
		return fragment;
	}
	
	
	public void setTangleId(int tangleId) {
		this.tangleId = tangleId;
	}

	public void setText(String text) {
		this.text = text;
	}

}