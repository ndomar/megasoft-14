package com.megasoft.entangle;

import com.google.android.gms.maps.GoogleMap;

import android.content.Intent;
import android.location.Location;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;

public class RequestEntryFragment extends Fragment implements OnClickListener {

	private View view;

	LocationManager locationmanager;
	String provider;
	Location location;
	String Target;
	double longitude;
	double latidue;
	GoogleMap map;

	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		this.view = inflater.inflate(R.layout.request_entry_fragment,
				container, false);

		setAttributes();

		return this.view;
	}

	private void setAttributes() {
		Bundle args = getArguments();
		((TextView) view.findViewById(R.id.request_entry_description))
				.setText(args.getString("description"));
		((TextView) view.findViewById(R.id.request_entry_requester_name))
				.setText(args.getString("requesterName"));
		((TextView) view.findViewById(R.id.request_entry_date))
				.setText("Created at" + args.getString("date"));
		((TextView) view.findViewById(R.id.request_entry_tags)).setText(args
				.getString("tags"));
		((TextView) view.findViewById(R.id.request_entry_price)).setText(args
				.getString("price"));
		if (args.get("deadline") != null) {
			((TextView) view.findViewById(R.id.request_entry_deadline))
					.setText(args.getString("deadline"));
		} else {
			((TextView) view.findViewById(R.id.request_entry_deadline))
					.setText("");
		}

		((TextView) view.findViewById(R.id.request_entry_status)).setText(args
				.getString("status"));
		((Button) view.findViewById(R.id.BRequestToLocation))
				.setOnClickListener(this);
		longitude = Double.parseDouble(args.getString("longitude"));
		latidue = Double.parseDouble(args.getString("latidue"));
		Log.e("Location", longitude + "");
		Log.e("Location", latidue + "");
	}

	@Override
	public void onClick(View arg0) {
		// TODO Auto-generated method stub
		switch (arg0.getId()) {
		case R.id.BRequestToLocation:

			Intent intent = new Intent(android.content.Intent.ACTION_VIEW,
					Uri.parse("http://maps.google.com/maps?q="
							+ Double.toString(latidue) + ","
							+ Double.toString(longitude)));
			startActivity(intent);

			break;
		}
	}
}
